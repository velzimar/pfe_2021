<?php

namespace App\Controller\API;

use App\Entity\DealCategory;
use App\Entity\OrderDeal;
use App\Entity\User;
use App\Repository\DealCategoryRepository;
use App\Repository\DeliveryRepository;
use App\Repository\OrderDealRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductOptionsRepository;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use ArrayObject;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/deal")
 * @method User|null getUser()
 */
class DealAPI extends AbstractFOSRestController
{

    private $productRepository;

    //  private $manager;

    function __construct(DealRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        // $this->manager = $this->getDoctrine()->getManager();
    }


    /**
     * @Rest\Post(name="DealAPI_byName", "/byName/")
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @QueryParam(name="name", nullable=false)
     */
    public function postbyNameAction(ParamFetcher $paramFetcher): Response
    {
        /*
        $data = json_decode($request->getContent(), true);
        if (!isset($data["nom"])) {
            $view = $this->view(["success" => false]);
            return $this->handleView($view);
        }
        $nom = $data["nom"];
        */
        $nom = $paramFetcher->get('name');
        $products = $this->productRepository->findByName($nom);
        $view = $this->view([
            'success' => true,
            'products' => $products,
        ]);
        return $this->handleView($view);
    }


    /**
     * @Rest\Post(name="DealAPI_byBusinessId_byName_withOptions", "/byBusinessIdbyNameWithOptions/")
     * @param ParamFetcher $paramFetcher
     * @param ProductOptionsRepository $optionsRepository
     * @param DeliveryRepository $deliveryRepository
     * @param ProductCategoryRepository $productCategoryRepository
     * @return Response
     * @QueryParam(name="name", nullable=false)
     * @QueryParam(name="id", nullable=false)
     */
    public function postbyNameWithOptionsAction_byBusinessId(
        ParamFetcher $paramFetcher,
        ProductOptionsRepository $optionsRepository,
        DeliveryRepository $deliveryRepository,
        ProductCategoryRepository $productCategoryRepository
    ): Response
    {
        /*
        $data = json_decode($request->getContent(), true);
        if (!isset($data["nom"])) {
            $view = $this->view(["success" => false]);
            return $this->handleView($view);
        }
        $nom = $data["nom"];
        */
        $nom = $paramFetcher->get('name');
        $id = $paramFetcher->get('id');
        if($id==null || $id ==""){
            $view = $this->view([
                'code' => 400,
                'success' => false,
            ]);
            return $this->handleView($view);
        }
        $path="http://192.168.1.101:8000/product_images/";
        $products = $this->productRepository->findByBusinessIdByName($id,$nom,$path);
        //added for delivery service
        $delivery = $deliveryRepository->findByBusinessId($id);
        //end delivery service

        //$ops = new ArrayObject();
        $productsWithOptions = [];
        foreach($products as $product){
            $thisProductOptions = $optionsRepository->findByProductId($product["id"]);
            array_push($productsWithOptions,$product+=["options"=>$thisProductOptions]);
        }
        $view = $this->view([
            'code' => 200,
            'business' => $id,
            'success' => $productsWithOptions==[]?false:true,
            'products' => $products,
            'productsWithOptions' => $productsWithOptions,
            'delivery' => $delivery,
            'imagesPath' => $path
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post(name="DealAPI_byBusinessId_byDealCategory_byName", "/byBusinessIdbyDealCategorybyName/")
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @QueryParam(name="name", nullable=false)
     * @QueryParam(name="id", nullable=false)
     * @QueryParam(name="categoryId", nullable=false)
     */
    public function postbyNameWithOptionsAction_byBusinessId_byProductCategoryAction(
        ParamFetcher $paramFetcher
    ): Response
    {
        $nom = $paramFetcher->get('name');
        $id = $paramFetcher->get('id');
        $categoryId = $paramFetcher->get('categoryId');
        if($id==null || $id ==""){
            $view = $this->view([
                'code' => 400,
                'success' => false,
            ]);
            return $this->handleView($view);
        }

        $path="http://192.168.1.101:8000/deal_images/";
        if($categoryId==null || $categoryId ==""){
            $products = $this->productRepository->findByBusinessIdByName($id,$nom,$path);
            $code = 200;
        }else{
            $products = $this->productRepository->findByBusinessIdByCategoryIdByName($id,$nom,$path,$categoryId);
            $code = 201;
        }

        $view = $this->view([
            'code' => $code,
            'business' => $id,
            'success' => !$products,
            'products' => $products,
            'imagesPath' => $path
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post(name="DealAPI_CategoriesByBusinessId_notEmpty", "/CategoriesByBusinessId_notEmpty/")
     * @param ParamFetcher $paramFetcher
     * @param DealCategoryRepository $productCategoryRepository
     * @return Response
     * @QueryParam(name="id", nullable=false)
     */
    public function postCategoriesByBusinessId_notEmptyAction(
        ParamFetcher $paramFetcher,
        DealCategoryRepository $productCategoryRepository
    ): Response
    {
        $businessId = $paramFetcher->get('id');

        if($businessId == null){
            $view = $this->view([
                'code' => 200
            ]);
            return $this->handleView($view);
        }
        $categories = $productCategoryRepository->findNotEmptyCategoriesByBusiness($businessId);
        if($categories == []){
            $view = $this->view([
                'code' => 300,
            ]);
            return $this->handleView($view);
        }
        $view = $this->view([
            'code' => 400,
            'categories' =>$categories
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post(name="DealAPI_postSendEmailContaintDealCodeAction", "/postSendEmailContaintDealCodeAction/")
     * @param Swift_Mailer $mailer
     * @param ParamFetcher $paramFetcher
     * @param DealRepository $dealRepository
     * @param UserRepository $userRepository
     * @return Response
     * @QueryParam(name="id", nullable=false)
     * @QueryParam(name="dealId", nullable=false)
     */
    public function postSendEmailContaintDealCodeAction(
        Swift_Mailer $mailer,
        ParamFetcher $paramFetcher,
        DealRepository $dealRepository,
        UserRepository $userRepository,
        OrderDealRepository $orderDealRepository
    ): Response
    {
        $userId = $paramFetcher->get('id');
        $dealId = $paramFetcher->get('dealId');

        if($dealId == null ||  $userId == null){
            $view = $this->view([
                'code' => 200 //check params
            ]);
            return $this->handleView($view);
        }
        //checking if there is more promo codes
        $deal = $dealRepository->find(["id"=>$dealId]);
        if($deal->getQtt()<=0){
            $view = $this->view([
                'code' => 202 //no more codes
            ]);
            return $this->handleView($view);
        }
        //getting the user infos
        $user = $userRepository->find(["id"=>$userId]);

        //verify if the user have used already this deal

        $used = $orderDealRepository->findOneBy(["user"=>$user,"deal"=>$deal]) !== null;
        //user already used the code
        if($used == true){
            $view = $this->view([
                'code' => 201 //already used code
            ]);
            return $this->handleView($view);
        }else{

            //saving data
            $order = new OrderDeal();
            $order->setBusiness($deal->getBusiness());
            $order->setDeal($deal);
            $order->setUser($user);
            //generate code
                $code = "123456";
            //end generate
            $order->setCode($code);
            $order->setIsUsed(false);
            $deal->setQtt($deal->getQtt()-1);
            $m = $this->getDoctrine()->getManager();
            $m->persist($order);
            $m->flush();
            //end saving data
            //sending email containing the code
                //getting infos about the business
                    $infos = $dealRepository->findBusinessInfosByDealId($dealId);
                    $phone = $infos[0]['businessPhone'];
                    $email = $infos[0]['businessEmail'];
                    $name = $infos[0]['businessName'];
                //end infos
            $message = (new Swift_Message("Voici le code promo"))
                ->setFrom("superadmin@looper.com")
                ->setTo($user->getEmail())
                ->setBody(
                    $this->render("/deal/dealCodeMessageToClient/dealCodeMessageToClient.html.twig",["code"=>$code, "phone"=>$phone, "email"=>$email, "name"=>$name]), 'text/html'
                );
            $mailer->send($message);
            //end mail sending


            $view = $this->view([
                'code' => 400,
                'infos' => $infos
            ]);
            return $this->handleView($view);
        }


    }




}
