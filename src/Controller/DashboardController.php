<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\DealCategory;
use App\Entity\User;
use App\Form\DealType;
use App\Form\SelectUserType;
use App\Repository\DealRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductRepository;
use App\Repository\SubOrderProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard")
 * @method User|null getUser()
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/all", name="dashboard", methods={"GET"})
     * @return Response
     */
    public function index( ): Response
    {
        return $this->render('sellerDashboard/index.html.twig', [
        ]);
    }

    //30days test

    /**
     * @Route("/products", name="products_dashboard", methods={"GET"})
     * @param ProductRepository $productRepository
     * @param UserRepository $userRepository
     * @param OrderProductRepository $orderProductRepository
     * @param SubOrderProductRepository $subOrderProductRepository
     * @return Response
     */
    public function products(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        OrderProductRepository $orderProductRepository,
        SubOrderProductRepository $subOrderProductRepository
    ): Response
    {
        $user = $this->getUser();
        $numberOfDays = 30;
        switch($numberOfDays){
            case 30:
                $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . " -30 day") );break;
            case 7:
                $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . " -7 day") );break;
            case 1:
                $myDate = date("Y-m-d");break;
            case 0:
                $myDate = date('l',strtotime(date('Y-01-01')));break;
            case -1:
                $myDate = "";break;
        }

        if($myDate !== ""){
            $nbProducts = $subOrderProductRepository->findProductsOrderByMostSold($myDate,$user);
            $nbCustomers = $orderProductRepository->findCustomersOrderByMostBought($myDate,$user);
            $qtt = $subOrderProductRepository->findSommeQTT($myDate,$user);
            $revenu = $orderProductRepository->findSommeTotal($myDate,$user);
            $clientsNb = $orderProductRepository->findCustomers($myDate,$user);
        }
        else{
            $nbProducts = $subOrderProductRepository->findProductsOrderByMostSold($myDate,$user);
            $nbCustomers = $orderProductRepository->findCustomersOrderByMostBought($myDate,$user);
            $qtt = $subOrderProductRepository->findSommeQTT($myDate,$user);
            $revenu = $orderProductRepository->findSommeTotal($myDate,$user);
            $clientsNb = $orderProductRepository->findCustomers($myDate,$user);
        }

        //dd($clientsNb);
        return $this->render('sellerDashboard/products_dashboard.html.twig', [
            "clients"=>$nbCustomers,
            "products"=>$nbProducts,
            "nbClient"=>$clientsNb,
            "revenu"=>$revenu,
            "nbProdSold"=>$qtt
        ]);
    }



}
