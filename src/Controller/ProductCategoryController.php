<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Entity\User;
use App\Form\ProductCategoryType;
use App\Form\SelectUserType;
use App\Form\SelectUserTypeForCategory;
use App\Repository\ProductCategoryRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
     * @Route("/admin/selectUser_{action}", name="selectUserForCategory", methods={"GET","POST"})
     * @param Request $request
     * @param $action
     * @return Response
     */
    public function selectUser(Request $request, $action): Response
    {
        $first = $this->createForm(SelectUserTypeForCategory::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->isValid()) {
            $userId = $first->get('businessId')->getData();
            $this->addFlash('success', "from selectUserForCategory $userId");

            return $this->redirectToRoute("user_category_admin_$action",[
                'userId' => $userId
            ],301);

        }
        return $this->render('product_category/selectUserForCategory.html.twig', [
            'form' => $first->createView(),
        ]);
    }

    /**
     * @Route("/admin/{userId}/new", name="user_category_admin_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $userId
     * @return Response
     */
    public function newForUser(Request $request, User $userId): Response
    {
        $productCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);
        //$id=$this->getUser()->getId();
        $productCategory->setBusinessId($userId);
        //$this->addFlash('success', "$id wtf");
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $entityManager->persist($productCategory);
                $entityManager->flush();
                return $this->redirectToRoute('category_index_user',['userId'=>$userId]);
            } catch(\Doctrine\ORM\ORMException $e){
                $this->get('session')->getFlashBag()->add('success', 'User already exists');
                $this->addFlash('success', "doctrine error");
                return $this->render('product_category/new.html.twig', [
                    'product_category' => $productCategory,
                    'form' => $form->createView(),
                ]);
                // flash msg
                // or some shortcut that need to be implemented
                // $this->addFlash('error', 'Custom message');
                // error logging - need customization
                //$this->get('logger')->error($e->getMessage());
                //$this->get('logger')->error($e->getTraceAsString());
                // or some shortcut that need to be implemented
                // $this->logError($e);
                // some redirection e. g. to referer
                //return $this->redirect($request->headers->get('referer'));
            } catch(\Exception $e){
                $this->addFlash('success', "doctrine error2");
                // other exceptions
                // flash
                // logger
                // redirection
                return $this->render('product_category/new.html.twig', [
                    'product_category' => $productCategory,
                    'form' => $form->createView(),
                ]);
            }
        }
        return $this->render('product_category/new.html.twig', [
            'product_category' => $productCategory,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{userId}/", name="category_index_user", methods={"GET","POST"})
     * @param ProductCategoryRepository $productRepository
     * @param User $userId
     * @return Response
     */
    public function userCategories(ProductCategoryRepository $productRepository, User $userId): Response
    {
        return $this->render('product_category/userCategories.html.twig', [
            'categories' => $productRepository->findByUser($userId),
            'userId' => $userId
        ]);
    }


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
     * @Route("/{id}", name="product_category_show", methods={"GET"})
     */
    public function show(ProductCategory $productCategory): Response
    {
        return $this->render('product_category/show.html.twig', [
            'product_category' => $productCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProductCategory $productCategory): Response
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
