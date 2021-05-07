<?php

namespace App\Controller\API;

use App\Entity\OrderProduct;
use App\Entity\SubOrderProduct;
use App\Entity\User;
use App\Repository\DeliveryRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductOptionsRepository;
use App\Repository\ProductRepository;
use App\Repository\SubOrderProductRepository;
use App\Repository\UserRepository;
use DateTime;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Util\OrderedHashMap;
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
     * @Rest\Post(name="ProductAPI_byBusinessId_byProductCategory_byName_withOptions", "/byBusinessIdbyProductCategorybyNameWithOptions/")
     * @param ParamFetcher $paramFetcher
     * @param ProductOptionsRepository $optionsRepository
     * @param DeliveryRepository $deliveryRepository
     * @param ProductCategoryRepository $productCategoryRepository
     * @return Response
     * @QueryParam(name="name", nullable=false)
     * @QueryParam(name="id", nullable=false)
     * @QueryParam(name="categoryId", nullable=false)
     */
    public function postbyNameWithOptionsAction_byBusinessId_byProductCategoryAction(
        ParamFetcher $paramFetcher,
        ProductOptionsRepository $optionsRepository,
        DeliveryRepository $deliveryRepository,
        ProductCategoryRepository $productCategoryRepository
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

        $path="http://192.168.1.101:8000/product_images/";
        if($categoryId==null || $categoryId ==""){
            $products = $this->productRepository->findByBusinessIdByName($id,$nom,$path);
            $code = 200;
        }else{
            $products = $this->productRepository->findByBusinessIdByCategoryIdByName($id,$nom,$path,$categoryId);
            $code = 201;
        }
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
            'code' => $code,
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
     * @Rest\Post(name="ProductAPI_CategoriesByBusinessId_notEmpty", "/CategoriesByBusinessId_notEmpty/")
     * @param ParamFetcher $paramFetcher
     * @param ProductCategoryRepository $productCategoryRepository
     * @return Response
     * @QueryParam(name="id", nullable=false)
     */
    public function postCategoriesByBusinessId_notEmptyAction(
        ParamFetcher $paramFetcher,
        ProductCategoryRepository $productCategoryRepository
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
     * @Rest\Post(name="ProductAPI_makeOrder", "/makeOrder/")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function postOrderAction(Request $request, UserRepository $userRepository, ProductRepository $productRepository): Response
    {
        $json = $request->getContent();
        //getting json body
        if ($decodedJson = json_decode($json, true)) {
            $data = $decodedJson;
        } else {
            $data = $request->request->all();
        }

        if ($request->isMethod('POST')) {
            //validate values
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $order = new OrderProduct();
                $order->setBusiness($userRepository->find($data["business"]));
                $order->setClient($userRepository->find($data["client"]));
                $order->setDelivery($data["delivery"]);
                $order->setOrderDate(new DateTime('now'));
                $order->setModifyDate(new dateTime("now"));
                $order->setTotal($data["total"]);
                $order->setPhone($data["phone"]);
                $order->setStatus("En attente");
                $entityManager->persist($order);
                $entityManager->flush();
                $productsList = $data["cart"];
                $cart = [];
                foreach ($productsList as $p){
                    $productId = $p["id"];
                    $qtt = $p["orderedQuantity"];
                    $price = $p["price"];
                    $optionsPrice = $p["optionsPrice"];
                    $name = $p["name"];
                    $options = $p["forSeller"];
                    $status = "En attente";
                    $subOrder = new SubOrderProduct();
                    $subOrder->setStatus($status);
                    $subOrder->setQtt($qtt);
                    if(sizeof($options)>0)
                        $subOrder->setOptions($options);
                    $subOrder->setName($name);
                    $subOrder->setOptionsPrice($optionsPrice);
                    $subOrder->setOrderProduct($order);

                    $subOrder->setOrderDate(new DateTime('now'));
                    $subOrder->setModifyDate(new dateTime("now"));
                    $subOrder->setPrice($price);
                    $subOrder->setProduct($productRepository->find($productId));

                    $entityManager->persist($subOrder);
                    $entityManager->flush();
                    array_push($cart,$p);
                }
               // $entityManager->persist($order);
             //   $entityManager->flush();

                $view = $this->view([
                    "success" => true,
                    "code" => 200,
                    "orderId" => $order->getId(),
                    "cart" => $cart
                ]);
                return $this->handleView($view);
            } catch (Exception $e) {
                $view = $this->view([
                    "success" => false,
                    "code" => 401,
                ]);
                return $this->handleView($view);
            }
        }
        $view = $this->view([
            "success" => false,
            "code" => 400,
            "message" => "Check request body"
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post(name="ProductAPI_getOrdersList", "/ordersList/")
     * @param ParamFetcher $paramFetcher
     * @param OrderProductRepository $orderProductRepository
     * @param SubOrderProductRepository $subOrderProductRepository
     * @return Response
     * @QueryParam(name="ClientId", nullable=false)
     * @QueryParam(name="Status", nullable=false)
     */
    public function postOrdersListOfClientByStatusAction(
        ParamFetcher $paramFetcher
        ,OrderProductRepository $orderProductRepository
        ,SubOrderProductRepository $subOrderProductRepository
    ): Response
    {
        $clientId = $paramFetcher->get('ClientId');
        $status = $paramFetcher->get('Status');
        $statusList = ["delivered","ready","inProgress","cancelled"];
        if($clientId == null || $status == null || !in_array($status,$statusList) ){
            $view = $this->view([
                'code' => 200
            ]);
            return $this->handleView($view);
        }
        switch ($status){
            case $statusList[0]:
                $status="Livrer";
                break;
            case $statusList[1]:
                $status="Pret";
                break;
            case $statusList[2]:
                $status="En attente";
                break;
            case $statusList[3]:
                $status="Annuler";
                break;
        }
        //dump($status);die;
        $orders = $orderProductRepository->findByUserByStatus($clientId,$status);
        $suborders = [];
        foreach($orders as $order){
            //array_push($suborders,$order["orderId"]);
            $orderId = $order["orderId"];
            $total = $order["total"];
            $create = $order["orderDate"];
            $modify = $order["modifyDate"];
            $status = $order["status"];
            $business = $order["business"];
            $delivery = $order["delivery"];
            $seen = $order["seen"];
            $businessName = $order["businessName"];
            $count = $subOrderProductRepository->findByUser_withSubOrder($orderId);
            array_push($suborders,[
                "orderId"=>$orderId,
                "total" =>$total,
                "create" =>$create,
                "modify" =>$modify,
                "status" =>$status,
                "seen"=>$seen,
                "business" =>$business,
                "delivery" =>$delivery,
                "businessName" =>$businessName,
                "count" =>sizeof($count),
                "suborders"=>$count
            ]);

        }



        if($orders == []){
            $view = $this->view([
                'code' => 300,
            ]);
            return $this->handleView($view);
        }
        $view = $this->view([
            'code' => 400,
            'orders' =>$suborders
        ]);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post(name="ProductAPI_makeItSeen", "/makeItSeen/")
     * @param ParamFetcher $paramFetcher
     * @param OrderProductRepository $orderProductRepository
     * @param SubOrderProductRepository $subOrderProductRepository
     * @return Response
     * @QueryParam(name="OrderId", nullable=false)
     */
    public function postMakeOrderSeenAction(
        ParamFetcher $paramFetcher
        ,OrderProductRepository $orderProductRepository
    ): Response
    {
        $OrderId = $paramFetcher->get('OrderId');
        if($OrderId == null ){
            $view = $this->view([
                'code' => 200
            ]);
            return $this->handleView($view);
        }
        //dump($status);die;
        $order = $orderProductRepository->find($OrderId);
        $order->setSeen(true);
        $m = $this->getDoctrine()->getManager();
        $m->persist($order);
        $m->flush();
        $view = $this->view([
            'code' => 400,
            'order' =>$order->getId()
        ]);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post(name="ProductAPI_NotSeenNumber", "/NotSeenNumber/")
     * @param ParamFetcher $paramFetcher
     * @param OrderProductRepository $orderProductRepository
     * @return Response
     * @QueryParam(name="ClientId", nullable=false)
     */
    public function postNotSeenNumberAction(
        ParamFetcher $paramFetcher
        ,OrderProductRepository $orderProductRepository
    ): Response
    {
        $clientId = $paramFetcher->get('ClientId');
        if($clientId == null ){
            $view = $this->view([
                'code' => 200
            ]);
            return $this->handleView($view);
        }
        //dump($status);die;
        $order = $orderProductRepository->findBy(["seen"=>false,"client"=>$clientId]);
        $view = $this->view([
            'code' => 400,
            'count' =>sizeof($order)
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
