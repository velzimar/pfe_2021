<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Entity\User;
use App\Form\ProductCategoryType;
use App\Form\SelectUserTypeForCategory;
use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/productCategory")
 * @method User|null getUser()
 */
class ProductCategoryController extends AbstractController
{
    /**
     * @Route("/", name="product_category_index", methods={"GET"})
     */
    public function index(ProductCategoryRepository $productCategoryRepository): Response
    {
        return $this->render('product_category/index.html.twig', [
            'product_categories' => $productCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/selectUserForCategory_{action}", name="user_product_category_index", methods={"GET","POST"})
     * @param Request $request
     * @param $action
     * @return Response
     */

    public function selectUserForCategory(Request $request, $action): Response
    {
        $first = $this->createForm(SelectUserTypeForCategory::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->isValid()) {
            $userId = $first->get('businessId')->getData();
            $this->addFlash('success', "from selectUser $userId");
            $this->addFlash('success', "action $action");

            return $this->redirectToRoute("userProductCategories_$action",[
                'userId' => $userId
            ],301);

        }
        return $this->render('product_category/selectUserForCategory.html.twig', [
            'form' => $first->createView(),
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/", name="userProductCategories_index", methods={"GET","POST"})
     * @param ProductCategoryRepository $productCategoryRepository
     * @param User $userId
     * @return Response
     */
    public function userProductCategories(ProductCategoryRepository $productCategoryRepository, User $userId): Response
    {
        return $this->render('product_category/userProductCategories.html.twig', [
            'product_categories' => $productCategoryRepository->findByUser($userId),
            'userId' => $userId
        ]);
    }

/*
    // when the user is mandatory (e.g. behind a firewall)
    public function fooAction(UserInterface $user)
    {
        $userId = $user->getId();
    }

    // when the user is optional (e.g. can be anonymous)
    public function barAction(UserInterface $user = null)
    {
        $userId = null !== $user ? $user->getId() : null;
    }
    */

    /**
     * @Route("/new", name="product_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $productCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);
        //$id=$this->getUser()->getId();
        $productCategory->setBusinessId($this->getUser());
        //$this->addFlash('success', "$id wtf");
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($productCategory);
            $entityManager->flush();

            return $this->redirectToRoute('product_category_index');
        }

        return $this->render('product_category/new.html.twig', [
            'product_category' => $productCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/{id}/show", name="product_category_show", methods={"GET"})
     */
    public function show(ProductCategory $productCategory, User $userId): Response
    {
        return $this->render('product_category/show.html.twig', [
            'product_category' => $productCategory,
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/{id}/edit", name="product_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProductCategory $productCategory, User $userId): Response
    {
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_category_index');
        }

        return $this->render('product_category/edit.html.twig', [
            'product_category' => $productCategory,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/{id}", name="product_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProductCategory $productCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($productCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_category_index');
    }
}
