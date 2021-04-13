<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\DeliveryRepository;
use App\Repository\ProductOptionsRepository;
use App\Repository\ProductRepository;
use ArrayObject;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/product")
 * @method User|null getUser()
 */
class ProductAPI extends AbstractFOSRestController
{

    private $productRepository;

    //  private $manager;

    function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        // $this->manager = $this->getDoctrine()->getManager();
    }


    /**
     * @Rest\Post(name="ProductAPI_byName", "/byName/")
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
     * @Rest\Post(name="ProductAPI_byName_withOptions", "/byNameWithOptions/")
     * @param ParamFetcher $paramFetcher
     * @param ProductOptionsRepository $optionsRepository
     * @return Response
     * @QueryParam(name="name", nullable=false)
     */
    public function postbyNameWithOptionsAction(ParamFetcher $paramFetcher, ProductOptionsRepository $optionsRepository): Response
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
        //$products = $this->productRepository->findByBusinessIdByName($id,$nom);

        //$ops = new ArrayObject();
        $productsWithOptions = [];
        foreach($products as $product){
            $thisProductOptions = $optionsRepository->findByProductId($product["id"]);
            array_push($productsWithOptions,$product+=["options"=>$thisProductOptions]);


        }
        $view = $this->view([
            'success' => true,
            'products' => $products,
            'productsWithOptions' => $productsWithOptions
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post(name="ProductAPI_byBusinessId_byName_withOptions", "/byBusinessIdbyNameWithOptions/")
     * @param ParamFetcher $paramFetcher
     * @param ProductOptionsRepository $optionsRepository
     * @param DeliveryRepository $deliveryRepository
     * @return Response
     * @QueryParam(name="name", nullable=false)
     * @QueryParam(name="id", nullable=false)
     */
    public function postbyNameWithOptionsAction_byBusinessId(ParamFetcher $paramFetcher, ProductOptionsRepository $optionsRepository, DeliveryRepository $deliveryRepository): Response
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
        $products = $this->productRepository->findByBusinessIdByName($id,$nom);
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
            'success' => true,
            'products' => $products,
            'productsWithOptions' => $productsWithOptions,
            'delivery' => $delivery
        ]);
        return $this->handleView($view);
    }
/*
    public function postListsAction(ParamFetcher $paramFetcher)
    {
        $title = $paramFetcher->get('title');
        if ($title) {
            $list = new TaskList();

            $preferences = new Preference();

            $preferences->setList($list);
            $list->setPreferences($preferences);

            $list->setTitle($title);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        return $this->view(['title' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }
*/


}
