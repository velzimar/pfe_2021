<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\OrderProduct;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\SelectUserType;
use App\Repository\ProductRepository;
use App\Repository\OrderProductRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/orderProduct")
 * @method User|null getUser()
 */
class OrderProductController extends AbstractController
{
    /**
     * @Route("/all", name="myReceivedOrderProducts", methods={"GET"})
     * @param OrderProductRepository $orderProductRepository
     * @return Response
     */
    public function index_user(OrderProductRepository $orderProductRepository): Response
    {
        $user = $this->getUser();
        return $this->render('orderProduct/index.html.twig', [
            'user' => $user,
            'products' => $orderProductRepository->findBy(["business"=>$user]),
        ]);
    }

    /**
     * @Route("/{status}", name="myReceivedOrderProducts_status", methods={"GET"},requirements={"status"="Tous|Pret|Livrer|Attente|Annuler"})
     * @param OrderProductRepository $orderProductRepository
     * @param string $status
     * @return Response
     */
    public function livrer_user(OrderProductRepository $orderProductRepository, string $status): Response
    {
        $user = $this->getUser();
        if($status == "Tous"){
            return $this->render('orderProduct/index.html.twig', [
                'user' => $user,
                'products' => $orderProductRepository->findBy(["business"=>$user]),
            ]);
        }
        switch($status){
            case "Pret";
                $status = "Prêt";
                break;
            case "Attente";
                $status = "En attente";
                break;
        }
        return $this->render('orderProduct/index.html.twig', [
            'user' => $user,
            'products' => $orderProductRepository->findBy(["business"=>$user, "status"=>$status]),
        ]);
    }


    /**
     * @Route("/changeStatus/", name="orderProduct_changeStatus", methods={"PUT"})
     * @param Request $request
     * @param OrderProductRepository $orderProductRepository
     * @return JsonResponse
     */
    public function changeStatus(Request $request, OrderProductRepository $orderProductRepository): JsonResponse
    {

        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {

            $id = $request->request->get("id");
            $status= $request->request->get("status");
            $order = $orderProductRepository->find($id);
            $order->setStatus($status);
            $order->setModifyDate(new dateTime("now"));
            $em->persist($order);
            $em->flush();

            return new JsonResponse([
                'success'  => true,
                'orderProduct' => $id,
                'status' => $order,
            ]);
        }

        return new JsonResponse([
            'success'  => false,
        ]);
    }





    //for admin

    /**
     * @Route("/admin/{id}/all", name="orderproduct_index_admin", methods={"GET"})
     * @param OrderProductRepository $orderProductRepository
     * @param User $user
     * @return Response
     */
    public function listAllOrdersOfUserByAdmin(OrderProductRepository $orderProductRepository, User $user): Response
    {

        return $this->render('orderProduct/userProducts.html.twig', [
            'user' => $user,
            'products' => $orderProductRepository->findBy(["business"=>$user]),
        ]);
    }

    /**
     * @Route("/admin/{id}/{status}", name="orderproduct_index_admin_specified", methods={"GET"},requirements={"status"="Tous|Pret|Livrer|Attente|Annuler"})
     * @param OrderProductRepository $orderProductRepository
     * @param User $userId
     * @param string $status
     * @return Response
     */
    public function listSpecifiedOrdersOfUserByAdmin(OrderProductRepository $orderProductRepository,User $userId, string $status): Response
    {
        if($status == "Tous"){
            return $this->render('orderProduct/userProducts.html.twig', [
                'user' => $userId,
                'products' => $orderProductRepository->findBy(["business"=>$userId]),
            ]);
        }
        switch($status){
            case "Pret";
                $status = "Prêt";
                break;
            case "Attente";
                $status = "En attente";
                break;
        }
        return $this->render('orderProduct/userProducts.html.twig', [
            'user' => $userId,
            'products' => $orderProductRepository->findBy(["business"=>$userId, "status"=>$status]),
        ]);
    }

    /**
     * @Route("/admin/selectUser_{action}", name="selectUserProduct_forOrderProducts", methods={"GET","POST"})
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
        return $this->render('orderProduct/selectUser.html.twig', [
            'form' => $first->createView(),
        ]);
    }


    /**
     * @Route("/admin/changeStatus/", name="orderProduct_changeStatus_by_admin", methods={"PUT"})
     * @param Request $request
     * @param OrderProductRepository $orderProductRepository
     * @return JsonResponse
     */

    public function changeStatusByAdmin(Request $request, OrderProductRepository $orderProductRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get("id");
            $status= $request->request->get("status");
            $order = $orderProductRepository->find($id);
            $order->setStatus($status);
            $order->setModifyDate(new dateTime("now"));
            $em->persist($order);
            $em->flush();

            return new JsonResponse([
                'success'  => true,
                'orderProduct' => $id,
                'status' => $order,
            ]);
        }

        return new JsonResponse([
            'success'  => false,
        ]);
    }

    // getLast4Orders

    /**
     * @param OrderProductRepository $rep
     * @return Response
     */
    public function getLast4Orders(OrderProductRepository $rep): Response
    {
        $user = $this->getUser();
        $orders = $rep->findLast4($user);
        //dump( $orders);die();
        return $this->render(
            'last4Orders.html.twig',
            array('orders' => $orders)
        );
    }


    // getLast4Clients

    /**
     * @param OrderProductRepository $rep
     * @return Response
     */
    public function getLast4Clients(OrderProductRepository $rep): Response
    {
        $user = $this->getUser();
        $clients = $rep->findLast4Clients($user);
       //dump( $clients);die();
        return $this->render(
            'last4Clients.html.twig',
            array('clients' => $clients)
        );
    }
}
