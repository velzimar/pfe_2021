<?php

namespace App\Controller;

use App\Entity\DealCategory;
use App\Entity\User;
use App\Form\DealCategoryType;
use App\Form\SelectUserType;
use App\Form\SelectUserTypeForCategory;
use App\Repository\DealCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dealCategory")
 * @method User|null getUser()
 */
class DealCategoryController extends AbstractController
{
    /**
     * @Route("/myDealCategories/list", name="myDealCategories", methods={"GET","POST"})
     */
    public function index(DealCategoryRepository $dealCategoryRepository): Response
    {
        return $this->render('deal_category/index.html.twig', [
            'deal_categories' => $dealCategoryRepository->findByUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/myDealCategories/{id}/show", name="myDealCategories_show", methods={"GET"})
     */
    public function myDealCategories_show(DealCategory $dealCategory): Response
    {
        return $this->render('deal_category/myDealCategories_show.html.twig', [
            'deal_category' => $dealCategory,
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/myDealCategories/{id}/edit", name="myDealCategories_edit", methods={"GET","POST"})
     */
    public function myDealCategories_edit(Request $request, DealCategory $dealCategory): Response
    {
        $form = $this->createForm(DealCategoryType::class, $dealCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('myDealCategories');
        }
        return $this->render('deal_category/myDealCategories_edit.html.twig', [
            'deal_category' => $dealCategory,
            'form' => $form->createView(),
            'userId' => $this->getUser()
        ]);
    }


    /**
     * @Route("/myDealCategories/new", name="myDealCategories_new", methods={"GET","POST"})
     */
    public function myDealCategories_new(Request $request): Response
    {
        $dealCategory = new DealCategory();
        $form = $this->createForm(DealCategoryType::class, $dealCategory);
        $form->handleRequest($request);
        $dealCategory->setBusinessId($this->getUser());
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dealCategory);
            $entityManager->flush();
            return $this->redirectToRoute('myDealCategories');
        }
        return $this->render('deal_category/myDealCategories_new.html.twig', [
            'deal_category' => $dealCategory,
            'form' => $form->createView(),
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/myDealCategories/{id}/delete", name="myDealCategories_delete", methods={"DELETE"})
     */
    public function myDealCategories_delete(Request $request, DealCategory $dealCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dealCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dealCategory);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myDealCategories');
    }

//ADMINISTRATION




    /**
     * @Route("/admin/selectUserForCategory_{action}", name="user_deal_category_index", methods={"GET","POST"})
     * @param Request $request
     * @param $action
     * @return Response
     */

    public function selectUserForCategory(Request $request, $action): Response
    {
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->get('id')->getData()!=null) {
            $userId = $first->get('id')->getData();
            $this->addFlash('success', "from selectUser $userId");
            $this->addFlash('success', "action $action");

            return $this->redirectToRoute("userDealCategories_$action",[
                'userId' => $userId
            ],301);

        }
        return $this->render('deal_category/selectUserForCategory.html.twig', [
            'form' => $first->createView(),
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/", name="userDealCategories_index", methods={"GET","POST"})
     * @param DealCategoryRepository $dealCategoryRepository
     * @param User $userId
     * @return Response
     */

    public function userDealCategories(DealCategoryRepository $dealCategoryRepository, User $userId): Response
    {
        return $this->render('deal_category/userDealCategories.html.twig', [
            'deal_categories' => $dealCategoryRepository->findByUser($userId),
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
     * @Route("/admin/user_{userId}/new", name="userDealCategories_new", methods={"GET","POST"})
     */

    public function new(Request $request, User $userId): Response
    {
        $dealCategory = new DealCategory();
        $form = $this->createForm(DealCategoryType::class, $dealCategory);
        $form->handleRequest($request);
        //$id=$this->getUser()->getId();
        $dealCategory->setBusinessId($userId);
        //$this->addFlash('success', "$id wtf");
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dealCategory);
            $entityManager->flush();

            return $this->redirectToRoute('userDealCategories_index',['userId'=>$userId]);
        }

        return $this->render('deal_category/new.html.twig', [
            'deal_category' => $dealCategory,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/{id}/show", name="deal_category_show", methods={"GET"})
     */

    public function show(DealCategory $dealCategory, User $userId): Response
    {
        return $this->render('deal_category/show.html.twig', [
            'deal_category' => $dealCategory,
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/user_{userId}/{id}/edit", name="deal_category_edit", methods={"GET","POST"})
     */

    public function edit(Request $request, DealCategory $dealCategory, User $userId): Response
    {
        $form = $this->createForm(DealCategoryType::class, $dealCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('userDealCategories_index',['userId'=>$userId]);
        }

        return $this->render('deal_category/edit.html.twig', [
            'deal_category' => $dealCategory,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("admin/user_{userId}/{id}/delete", name="deal_category_delete", methods={"DELETE"})
     */

    public function delete(Request $request, DealCategory $dealCategory, User $userId): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dealCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dealCategory);
            $entityManager->flush();
        }
        return $this->redirectToRoute('userDealCategories_index',['userId'=>$userId]);
    }

}
