<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\DealCategory;
use App\Entity\User;
use App\Form\DealType;
use App\Form\SelectUserType;
use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/deal")
 * @method User|null getUser()
 */
class DealController extends AbstractController
{
    /**
     * @Route("/admin/all", name="deal_index", methods={"GET"})
     * @param DealRepository $dealRepository
     * @return Response
     */
    public function index(DealRepository $dealRepository): Response
    {
        return $this->render('deal/index.html.twig', [
            'deals' => $dealRepository->findAll(),
        ]);
    }


    /**
     * @Route("/admin/selectUser_{action}", name="selectUserDeal", methods={"GET","POST"})
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
            return $this->redirectToRoute("deal_$action",[
                'userId' => $userId
            ],301);
        }
        return $this->render('deal/selectUser.html.twig', [
            'form' => $first->createView(),
        ]);
    }


    /**
     * @Route("/admin/{userId}/list", name="deal_index_user", methods={"GET","POST"})
     * @param DealRepository $dealRepository
     * @param User $userId
     * @return Response
     */
    public function userDeals(DealRepository $dealRepository, User $userId): Response
    {
        return $this->render('deal/userDeals.html.twig', [
            'deals' => $dealRepository->findByUser($userId),
            'userId' => $userId
        ]);
    }


    /**
     * @Route("/admin/{userId}/{categoryId}/list", name="deal_byCategory_index_user", methods={"GET","POST"})
     * @param DealRepository $dealRepository
     * @param User $userId
     * @return Response
     */
    public function userDealsByCategory(DealRepository $dealRepository, User $userId, DealCategory $categoryId): Response
    {
        return $this->render('deal/userDeals.html.twig', [
            'deals' => $dealRepository->findByUserByCategory($userId,$categoryId),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/{userId}/new", name="deal_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $userId
     * @return Response
     */
    public function new(Request $request, User $userId): Response
    {
        //echo $userId->getId();
        $deal = new Deal();
        //$form = $this->createForm(ProductType::class, $product);
        //$form->handleRequest($request);
        $user = $this->getUser();
        if($userId == "" or $userId == null )
            return $this->redirectToRoute('selectUserDeal');
        $this->addFlash('success', "Utilisateur $user");
        $this->addFlash('success', "selected user1 $userId");
        $form = $this->createForm(DealType::class, $deal,
            ['userId' => $userId //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );
        //$role = $user->getRoles();
        //foreach($role as $rolee){
        //    $this->addFlash('success', "role user: $rolee");
        //}
        $categories = $userId->getDealCategories();
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
        $deal->setBusiness($userId);
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
            $entityManager->persist($deal);
            $entityManager->flush();
            return $this->redirectToRoute('deal_index_user',[
                'userId' => $userId
            ],301);
        }
        return $this->render('deal/new.html.twig', [
            'deal' => $deal,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/{userId}/{id}/show", name="deal_show", methods={"GET","POST"})
     * @param Deal $deal
     * @param User $userId
     * @return Response
     */
    public function show(Deal $deal, User $userId): Response
    {
        return $this->render('deal/show.html.twig', [
            'deal' => $deal,
            'userId' => $userId
        ]);
    }


    /**
     * @Route("admin/{userId}/{id}/delete", name="deal_delete", methods={"DELETE"})
     * @param Request $request
     * @param Deal $deal
     * @param User $userId
     * @return Response
     */
    public function delete(Request $request, Deal $deal, User $userId): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($deal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('deal_index_user',[
            'userId' => $userId
        ],301);
    }

    /**
     * @Route("/myDeals/list", name="myDeals", methods={"GET","POST"})
     * @param DealRepository $dealRepository
     * @return Response
     */
    public function myDeals(DealRepository $dealRepository): Response
    {
        return $this->render('deal/index.html.twig', [
            'deals' => $dealRepository->findByUser($this->getUser()),
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/myDeals/{categoryId}/list", name="myDeals_byCategory", methods={"GET","POST"})
     * @param DealRepository $dealRepository
     * @param DealCategory $categoryId
     * @return Response
     */
    public function myDealsByCategory(DealRepository $dealRepository, DealCategory $categoryId): Response
    {
        return $this->render('deal/userDeals.html.twig', [
            'deals' => $dealRepository->findByUserByCategory($this->getUser(),$categoryId),
            'userId' => $this->getUser()
        ]);
    }



    /**
     * @Route("/myDeals/new", name="myDeals_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function myDeals_new(Request $request): Response
    {
        $deal = new Deal();
        $user = $this->getUser();
        $this->addFlash('success', "Utilisateur $user");
        $form = $this->createForm(DealType::class, $deal,
            ['userId' => $user //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );
        $categories = $user->getDealCategories();
        foreach($categories as $category){
            if($category->getNom()=="defaultCategory"){
                $this->addFlash('success', "this is: $category");
                $form->get('category')->setData($category);
                //echo $product->getCategory();
            }else
                $this->addFlash('success', "categories de ce user: $category");
        }
        $form->handleRequest($request);
        $deal->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($deal);
            $entityManager->flush();
            return $this->redirectToRoute('myDeals');
        }
        return $this->render('deal/myDeals_new.html.twig', [
            'deal' => $deal,
            'form' => $form->createView(),
            'userId'=>$this->getUser()
        ]);
    }



    /**
     * @Route("/myDeals/{id}/show", name="myDeals_show", methods={"GET","POST"})
     * @param Deal $deal
     * @return Response
     */
    public function myDeals_show(Deal $deal): Response
    {
        return $this->render('deal/myDeals_show.html.twig', [
            'deal' => $deal,
            'userId' => $this->getUser()
        ]);
    }

    /**
     * @Route("/admin/{userId}/{id}/edit", name="deal_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Deal $deal
     * @param User $userId
     * @return Response
     */
    public function edit(Request $request, Deal $deal, User $userId): Response
    {
        //echo $userId->getId();
        //$form = $this->createForm(ProductType::class, $product);
        //$form->handleRequest($request);
        $user = $this->getUser();

        if($userId == "" or $userId == null )
            return $this->redirectToRoute('selectUserDeal');

        $this->addFlash('success', "Utilisateur $user");
        $this->addFlash('success', "selected user1 $userId");
        $form = $this->createForm(DealType::class, $deal,
            ['userId' => $userId //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );
        $form->handleRequest($request);
        $deal->setBusiness($userId);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('imageFile')->getData()==null){
                $this->addFlash('success', "its null");

                $deal->setImageFile(null);
                $deal->setFileName(null);
                $this->getDoctrine()->getManager()->persist($deal);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('deal_index_user',[
                'userId' => $userId
            ],301);
        }

        return $this->render('deal/edit.html.twig', [
            'deal' => $deal,
            'userId'=> $userId,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/myDeals/{id}/edit", name="myDeals_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Deal $deal
     * @return Response
     */
    public function myDeals_edit(Request $request, Deal $deal): Response
    {
        $user = $this->getUser();
        $this->addFlash('success', "Utilisateur $user");
        $form = $this->createForm(DealType::class, $deal,
            [
                'userId' => $user //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')
            ]
        );
        $form->handleRequest($request);
        $deal->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('imageFile')->getData()==null){
                $this->addFlash('success', "its null");
                $deal->setImageFile(null);
                $deal->setFileName(null);
                $this->getDoctrine()->getManager()->persist($deal);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('myDeals');
        }

        return $this->render('deal/myDeals_edit.html.twig', [
            'deal' => $deal,
            'userId'=> $user,
            'form' => $form->createView(),
        ]);
    }





    /**
     * @Route("myDeals/{id}/delete", name="myDeals_delete", methods={"DELETE"})
     * @param Request $request
     * @param Deal $deal
     * @return Response
     */
    public function MyDeals_delete(Request $request, Deal $deal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($deal);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myDeals');
    }

}
