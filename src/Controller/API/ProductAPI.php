<?php

namespace App\Controller\API;

use App\Entity\ProductCategory;
use App\Entity\User;
use App\Repository\DeliveryRepository;
use App\Repository\ProductCategoryRepository;
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
