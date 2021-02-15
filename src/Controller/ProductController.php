<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\SelectUserType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/", name="product_index", methods={"GET"})
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
     * @Route("/selectUser_{action}", name="selectUser", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function selectUser(Request $request, $action): Response
    {
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->isValid()) {
            $userId = $first->get('business')->getData();
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
     * @Route("/{userId}/", name="product_index_user", methods={"GET","POST"})
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
     * @Route("/{userId}/new", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $userId
     * @return Response
     */
    public function new(Request $request, User $userId): Response
    {
        echo $userId->getId();
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

            $form->handleRequest($request);

            $categories = $userId->getProductCategories();
            foreach($categories as $category){
                $this->addFlash('success', "categories de ce user: $category");
            }

            //$product->setBusiness($user);
            $product->setBusiness($userId);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('product_index_user',[
                    'userId' => $userId
                ],301);
            }

/*

        $form = $this->createForm(ProductType::class, $product,
            ['userId' => $user->getId() //or whatever the variable is called
            ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );

        //$role = $user->getRoles();
        //foreach($role as $rolee){
        //    $this->addFlash('success', "role user: $rolee");
        //}

        $form->handleRequest($request);

        $categories = $user->getProductCategories();
        foreach($categories as $category){
            $this->addFlash('success', "categories de ce user: $category");
        }

        $product->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }
*/
        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);


    }

    /**
     * @Route("/{userId}/{id}/show", name="product_show", methods={"GET","POST"})
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
     * @Route("/{userId}/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @param User $userId
     * @return Response
     */
    public function edit(Request $request, Product $product, User $userId): Response
    {

        echo $userId->getId();
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
     * @Route("/{userId}/{id}", name="product_delete", methods={"DELETE"})
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

}
