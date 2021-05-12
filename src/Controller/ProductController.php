<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\SelectUserType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 * @method User|null getUser()
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/admin/all", name="product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }


    /**
     * @Route("/admin/selectUser_{action}", name="selectUser", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function selectUser(Request $request, $action): Response
    {
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->get('id')->getData() != null) {
            $userId = $first->get('id')->getData();
            $this->addFlash('success', "from selectUser $userId");
            return $this->redirectToRoute("product_$action",[
                'userId' => $userId
            ],301);
        }
        return $this->render('product/selectUser.html.twig', [
            'form' => $first->createView(),
        ]);
    }


    /**
     * @Route("/admin/{userId}/list", name="product_index_user", methods={"GET","POST"})
     * @param ProductRepository $productRepository
     * @param User $userId
     * @return Response
     */
    public function userProducts(ProductRepository $productRepository, User $userId): Response
    {
        return $this->render('product/userProducts.html.twig', [
            'products' => $productRepository->findByUser($userId),
            'userId' => $userId
        ]);
    }
    /**
     * @Route("/myProducts/list", name="myProducts", methods={"GET","POST"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function myProducts(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findByUser($this->getUser()),
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/admin/{userId}/{categoryId}/list", name="product_byCategory_index_user", methods={"GET","POST"})
     * @param ProductRepository $productRepository
     * @param User $userId
     * @return Response
     */
    public function userProductsByCategory(ProductRepository $productRepository, User $userId, ProductCategory $categoryId): Response
    {
        return $this->render('product/userProducts.html.twig', [
            'products' => $productRepository->findByUserByCategory($userId,$categoryId),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/myProducts/{categoryId}/list", name="myProducts_byCategory", methods={"GET","POST"})
     * @param ProductRepository $productRepository
     * @param ProductCategory $categoryId
     * @return Response
     */
    public function myProductsByCategory(ProductRepository $productRepository, ProductCategory $categoryId): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findByUserByCategory($this->getUser(),$categoryId),
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/admin/{userId}/new", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $userId
     * @return Response
     */
    public function new(Request $request, User $userId): Response
    {
        //echo $userId->getId();
        $product = new Product();
        //$form = $this->createForm(ProductType::class, $product);
        //$form->handleRequest($request);
        $user = $this->getUser();
        if($userId == "" or $userId == null )
            return $this->redirectToRoute('selectUser');
            $this->addFlash('success', "Utilisateur $user");
            $this->addFlash('success', "selected user1 $userId");
            $form = $this->createForm(ProductType::class, $product,
                ['userId' => $userId //or whatever the variable is called
                    ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
            );
            //$role = $user->getRoles();
            //foreach($role as $rolee){
            //    $this->addFlash('success', "role user: $rolee");
            //}
        $categories = $userId->getProductCategories();
        foreach($categories as $category){
            if($category->getNom()=="defaultCategory"){
                $this->addFlash('success', "this is: $category");
                 $form->get('category')->setData($category);
                //echo $product->getCategory();
            }else
                $this->addFlash('success', "categories de ce user: $category");
        }
            $form->handleRequest($request);
            //$product->setBusiness($user);
            $product->setBusiness($userId);
            if ($form->isSubmitted() && $form->isValid()) {
                /*
                if($form->get('imageFile')->getData()==null){

                    $this->addFlash('success', "its null");
                    echo "its null";
                    return $this->render('product/new.html.twig', [
                        'product' => $product,
                        'form' => $form->createView(),
                    ]);
                }
                */
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('product_index_user',[
                    'userId' => $userId
                ],301);
            }
        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }


    /**
     * @Route("/myProducts/new", name="myProducts_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function myProducts_new(Request $request): Response
    {
        $product = new Product();
        $user = $this->getUser();
        $this->addFlash('success', "Utilisateur $user");
        $form = $this->createForm(ProductType::class, $product,
            ['userId' => $user //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );
        $categories = $user->getProductCategories();
        foreach($categories as $category){
            if($category->getNom()=="defaultCategory"){
                $this->addFlash('success', "this is: $category");
                $form->get('category')->setData($category);
                //echo $product->getCategory();
            }else
                $this->addFlash('success', "categories de ce user: $category");
        }
        $form->handleRequest($request);
        $product->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('myProducts');
        }
        return $this->render('product/myProducts_new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'userId'=>$this->getUser()
        ]);
    }


    /**
     * @Route("/admin/{userId}/{id}/show", name="product_show", methods={"GET","POST"})
     * @param Product $product
     * @param User $userId
     * @return Response
     */
    public function show(Product $product, User $userId): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/myProducts/{id}/show", name="myProducts_show", methods={"GET","POST"})
     * @param Product $product
     * @return Response
     */
    public function myProducts_show(Product $product): Response
    {
        return $this->render('product/myProducts_show.html.twig', [
            'product' => $product,
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/admin/{userId}/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @param User $userId
     * @return Response
     */
    public function edit(Request $request, Product $product, User $userId): Response
    {
        //echo $userId->getId();
        //$form = $this->createForm(ProductType::class, $product);
        //$form->handleRequest($request);
        $user = $this->getUser();

        if($userId == "" or $userId == null )
            return $this->redirectToRoute('selectUser');

        $this->addFlash('success', "Utilisateur $user");
        $this->addFlash('success', "selected user1 $userId");
        $form = $this->createForm(ProductType::class, $product,
            ['userId' => $userId //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );
        $form->handleRequest($request);
        $product->setBusiness($userId);
        if ($form->isSubmitted() && $form->isValid()) {
/*
            if ($form->get('imageFile')->getData()==null){
                $this->addFlash('success', "its null");

                $product->setImageFile(null);
                $product->setFileName(null);
                $this->getDoctrine()->getManager()->persist($product);
            }
*/
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('product_index_user',[
                'userId' => $userId
            ],301);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'userId'=> $userId,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/myProducts/{id}/edit", name="myProducts_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function myProducts_edit(Request $request, Product $product): Response
    {
        $user = $this->getUser();
        $this->addFlash('success', "Utilisateur $user");
        $form = $this->createForm(ProductType::class, $product,
            [
                'userId' => $user //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')
            ]
        );
        $form->handleRequest($request);
        $product->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            /*
            if ($form->get('imageFile')->getData()==null){
                $this->addFlash('success', "its null");
                $product->setImageFile(null);
                $product->setFileName(null);
                $this->getDoctrine()->getManager()->persist($product);
            }
            */
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('myProducts');
        }

        return $this->render('product/myProducts_edit.html.twig', [
            'product' => $product,
            'userId'=> $user,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/admin/{userId}/{id}/delete", name="product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @param User $userId
     * @return Response
     */
    public function delete(Request $request, Product $product, User $userId): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index_user',[
            'userId' => $userId
        ],301);
    }

    /**
     * @Route("/myProducts/{id}/delete", name="myProducts_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function MyProducts_delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myProducts');
    }

    /**
     * @Route("/get/getMaxPriority/{user}/{productCategory}", name="getMaxPriority", methods={"GET"})
     * @param Request $request
     * @param User $user
     * @param ProductCategory $productCategory
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getMaxPriority(Request $request, User $user, ProductCategory $productCategory, ProductRepository $productRepository): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $maxPriority = $productRepository->findMaxPriority(intval($user->getId()),intval($productCategory->getId()));
            return new JsonResponse([
                'success'  => true,
                'maxPriority' => $maxPriority[0]["max_priority"],
            ]);

        }
        $maxPriority = $productRepository->findMaxPriority(intval($user->getId()),intval($productCategory->getId()));
        return new JsonResponse([
            'success'  => true,
            'maxPriority' => $maxPriority[0]["max_priority"],
        ]);
        return new JsonResponse([
            'success'  => false,
        ]);
    }

    /**
     * @Route("/get/getMinPriority/{user}/{productCategory}", name="getMinPriority", methods={"GET"})
     * @param Request $request
     * @param User $user
     * @param ProductCategory $productCategory
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getMinPriority(Request $request, User $user, ProductCategory $productCategory, ProductRepository $productRepository): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $minPriority = $productRepository->findMinPriority(intval($user->getId()),intval($productCategory->getId()));
            dump($minPriority);dump($minPriority[0]["min_priority"]);
            return new JsonResponse([
                'success'  => true,
                'minPriority' => $minPriority[0]["min_priority"],
            ]);

        }
        $minPriority = $productRepository->findMinPriority(intval($user->getId()),intval($productCategory->getId()));
        return new JsonResponse([
            'success'  => true,
            'minPriority' => $minPriority[0]["min_priority"],
        ]);
        return new JsonResponse([
            'success'  => false,
        ]);
    }
    /**
     * @Route("/get/getProductsOfThisCategory/{user}/{productCategory}", name="getProductsOfThisCategory", methods={"GET"})
     * @param Request $request
     * @param User $user
     * @param ProductCategory $productCategory
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getProductsOfThisCategory(Request $request, User $user, ProductCategory $productCategory, ProductRepository $productRepository): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $minPriority = $productRepository->findProductsOfThisCategory(intval($user->getId()),intval($productCategory->getId()));
            dump($minPriority);
            return new JsonResponse([
                'success'  => true,
                'minPriority' => $minPriority,
            ]);

        }
        $minPriority = $productRepository->findProductsOfThisCategory(intval($user->getId()),intval($productCategory->getId()));
        return new JsonResponse([
            'success'  => true,
            'minPriority' => $minPriority,
        ]);
        return new JsonResponse([
            'success'  => false,
        ]);
    }

}
