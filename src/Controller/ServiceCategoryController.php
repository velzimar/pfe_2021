<?php

namespace App\Controller;

use App\Entity\ServiceCategory;
use App\Entity\User;
use App\Form\ServiceCategoryType;
use App\Form\SelectUserType;
use App\Repository\ServiceCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/serviceCategory")
 * @method User|null getUser()
 */
class ServiceCategoryController extends AbstractController
{
    /**
     * @Route("/myServiceCategories/list", name="myServiceCategories", methods={"GET","POST"})
     */
    public function index(ServiceCategoryRepository $serviceCategoryRepository): Response
    {
        return $this->render('service_category/index.html.twig', [
            'service_categories' => $serviceCategoryRepository->findByUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/myServiceCategories/{id}/show", name="myServiceCategories_show", methods={"GET"})
     */
    public function myServiceCategory_show(ServiceCategory $serviceCategory): Response
    {
        return $this->render('service_category/myServiceCategories_show.html.twig', [
            'service_category' => $serviceCategory,
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/myServiceCategories/{id}/edit", name="myServiceCategories_edit", methods={"GET","POST"})
     */
    public function myServiceCategory_edit(Request $request, ServiceCategory $serviceCategory): Response
    {
        $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('myServiceCategories');
        }
        return $this->render('service_category/myServiceCategories_edit.html.twig', [
            'service_category' => $serviceCategory,
            'form' => $form->createView(),
            'userId' => $this->getUser()
        ]);
    }


    /**
     * @Route("/myServiceCategories/new", name="myServiceCategories_new", methods={"GET","POST"})
     */
    public function myServiceCategory_new(Request $request): Response
    {
        $serviceCategory = new ServiceCategory();
        $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
        $form->handleRequest($request);
        $serviceCategory->setBusinessId($this->getUser());
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($serviceCategory);
            $entityManager->flush();
            return $this->redirectToRoute('myServiceCategories');
        }
        return $this->render('service_category/myServiceCategories_new.html.twig', [
            'service_category' => $serviceCategory,
            'form' => $form->createView(),
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/myServiceCategories/{id}/delete", name="myServiceCategories_delete", methods={"DELETE"})
     */
    public function myServiceCategory_delete(Request $request, ServiceCategory $serviceCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($serviceCategory);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myServiceCategories');
    }


    /**
     * @Route("/admin/selectUserForCategory_{action}", name="user_service_category_index", methods={"GET","POST"})
     * @param Request $request
     * @param $action
     * @return Response
     */
    public function selectUserForCategory(Request $request, $action): Response
    {
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->get('id')->getData() != null) {
            $userId = $first->get('id')->getData();
            return $this->redirectToRoute("userServiceCategories_$action",[
                'userId' => $userId
            ],301);
        }
        return $this->render('service_category/selectUserForCategory.html.twig', [
            'form' => $first->createView(),
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/", name="userServiceCategories_index", methods={"GET","POST"})
     * @param ServiceCategoryRepository $serviceCategoryRepository
     * @param User $userId
     * @return Response
     */
    public function userServiceCategories(ServiceCategoryRepository $serviceCategoryRepository, User $userId): Response
    {
        return $this->render('service_category/userServiceCategories.html.twig', [
            'service_categories' => $serviceCategoryRepository->findByUser($userId),
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
     * @Route("/admin/user_{userId}/new", name="userServiceCategories_new", methods={"GET","POST"})
     */
    public function new(Request $request, User $userId): Response
    {
        $serviceCategory = new ServiceCategory();
        $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
        $form->handleRequest($request);
        //$id=$this->getUser()->getId();
        $serviceCategory->setBusinessId($userId);
        //$this->addFlash('success', "$id wtf");
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($serviceCategory);
            $entityManager->flush();

            return $this->redirectToRoute('userServiceCategories_index',['userId'=>$userId]);
        }

        return $this->render('service_category/new.html.twig', [
            'service_category' => $serviceCategory,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/{id}/show", name="service_category_show", methods={"GET"})
     */
    public function show(ServiceCategory $serviceCategory, User $userId): Response
    {
        return $this->render('service_category/show.html.twig', [
            'service_category' => $serviceCategory,
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/{id}/edit", name="service_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ServiceCategory $serviceCategory, User $userId): Response
    {
        $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('userServiceCategories_index',['userId'=>$userId]);
        }

        return $this->render('service_category/edit.html.twig', [
            'service_category' => $serviceCategory,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("admin/user_{userId}/{id}/delete", name="service_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ServiceCategory $serviceCategory, User $userId): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($serviceCategory);
            $entityManager->flush();
        }
        return $this->redirectToRoute('userServiceCategories_index',['userId'=>$userId]);
    }
}
