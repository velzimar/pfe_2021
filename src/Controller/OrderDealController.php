<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\DealCategory;
use App\Entity\OrderDeal;
use App\Entity\User;
use App\Form\DealType;
use App\Form\SelectUserType;
use App\Repository\DealRepository;
use App\Repository\OrderDealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/orderDeal")
 * @method User|null getUser()
 */
class OrderDealController extends AbstractController
{
    /**
     * @Route("/admin/{id}/all", name="orderdeal_index_admin", methods={"GET"})
     * @param User $user
     * @param OrderDealRepository $orderDealRepository
     * @return Response
     */
    public function index_admin(User $user, OrderDealRepository $orderDealRepository): Response
    {
        return $this->render('orderDeal/userDeals.html.twig', [
            'user' => $user,
            'deals' => $orderDealRepository->findBy(["business"=>$user, "isUsed" => false]),
        ]);
    }

    /**
     * @Route("/admin/selectUser_{action}", name="selectUserDeal_forOrderDeals", methods={"GET","POST"})
     * @param Request $request
     * @param $action
     * @return Response
     */
    public function selectUser(Request $request, $action): Response
    {
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->get('id')->getData()!=null) {
            $userId = $first->get('id')->getData();
            $this->addFlash('success', "from selectUser $userId");
            return $this->redirectToRoute("$action",[
                'id' => $userId
            ],301);
        }
        return $this->render('orderDeal/selectUser.html.twig', [
            'form' => $first->createView(),
        ]);
    }

    /**
     * @Route("/admin/setIsUsed/{id}/{user}", name="setIsUsed_orderDeal_admin", methods={"GET","POST"})
     * @param OrderDeal $orderDeal
     * @param User $user
     * @return Response
     */
    public function toggleActiveAdmin(OrderDeal $orderDeal, User $user): Response
    {
        $orderDeal->setIsUsed(1);
        $manager = $this->getDoctrine()->getManager();
        $manager->flush();
        return $this->redirectToRoute('orderdeal_index_admin',['id'=>$user]);
    }

    /**
     * @Route("/all", name="myReceivedOrderDeals", methods={"GET"})
     * @param OrderDealRepository $orderDealRepository
     * @return Response
     */
    public function index_user(OrderDealRepository $orderDealRepository): Response
    {
        $user = $this->getUser();
        return $this->render('orderDeal/index.html.twig', [
            'user' => $user,
            'deals' => $orderDealRepository->findBy(["business"=>$user, "isUsed" => false]),
        ]);
    }

    /**
     * @Route("/setIsUsed/{id}", name="setIsUsed_orderDeal", methods={"GET","POST"})
     * @param OrderDeal $orderDeal
     * @return Response
     */
    public function toggleActive(OrderDeal $orderDeal): Response
    {
        $orderDeal->setIsUsed(1);
        $manager = $this->getDoctrine()->getManager();
        $manager->flush();
        return $this->redirectToRoute('myReceivedOrderDeals');
    }

}
