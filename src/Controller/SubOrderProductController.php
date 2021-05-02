<?php

namespace App\Controller;

use App\Entity\OrderProduct;
use App\Entity\User;
use App\Form\SelectUserType;
use App\Repository\SubOrderProductRepository ;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subOrderProduct")
 * @method User|null getUser()
 */
class SubOrderProductController extends AbstractController
{
    /**
     * @Route("/{orderId}/all", name="myReceivedSubOrderProducts", methods={"GET"})
     * @param OrderProduct $orderProduct
     * @param SubOrderProductRepository $subOrderProductRepository
     * @return Response
     */
    public function index_user(OrderProduct $orderProduct, SubOrderProductRepository $subOrderProductRepository): Response
    {
        $user = $this->getUser();
        return $this->render('subOrderProduct/index_subOrder.html.twig', [
            'orderId'=>$orderProduct,
            'user' => $user,
            'products' => $subOrderProductRepository->findBy(["orderProduct"=>$orderProduct]),
        ]);
    }

    /**
     * @Route("/{orderId}/{status}", name="myReceivedSubOrderProducts_status", methods={"GET"},requirements={"status"="Tous|Pret|Attente|Annuler"})
     * @param OrderProduct $orderProduct
     * @param SubOrderProductRepository $subOrderProductRepository
     * @param string $status
     * @return Response
     */
    public function byStatus_user(OrderProduct $orderId,SubOrderProductRepository $subOrderProductRepository, string $status): Response
    {
        $user = $this->getUser();
        if($status == "Tous"){
            return $this->render('subOrderProduct/index_subOrder.html.twig', [
                'orderId'=>$orderId,
                'user' => $user,
                'products' => $subOrderProductRepository->findBy(["orderProduct"=>$orderId]),
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
        return $this->render('subOrderProduct/index_subOrder.html.twig', [
            'orderId'=>$orderId,
            'user' => $user,
            'products' => $subOrderProductRepository->findBy(["orderProduct"=>$orderId, "status"=>$status]),
        ]);
    }


    /**
     * @Route("/changeStatus/", name="subOrderProduct_changeStatus", methods={"PUT"})
     * @param Request $request
     * @param SubOrderProductRepository $subOrderProductRepository
     * @return JsonResponse
     */
    public function changeStatus(Request $request, SubOrderProductRepository $subOrderProductRepository): JsonResponse
    {

        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get("id");
            $status= $request->request->get("status");
            $subOrder = $subOrderProductRepository->find($id);
            $subOrder->setStatus($status);
            $subOrder->setModifyDate(new dateTime("now"));
            $em->persist($subOrder);
            $em->flush();
            return new JsonResponse([
                'success'  => true,
                'orderProduct' => $id,
                'status' => $subOrder,
            ]);
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }





    //for admin
    /**
     * @Route("/admin/{orderId}/{id}/{status}", name="subOrderproduct_index_admin_specified", methods={"GET"},requirements={"status"="Tous|Pret|Attente|Annuler"})
     * @param SubOrderProductRepository $subOrderProductRepository
     * @param OrderProduct $orderId
     * @param User $userId
     * @param string $status
     * @return Response
     */
    public function listSpecifiedOrdersOfUserByAdmin(SubOrderProductRepository $subOrderProductRepository,OrderProduct $orderId ,User $userId, string $status): Response
    {
        if($status == "Tous"){
            return $this->render('subOrderProduct/userSubProducts.html.twig', [
                'orderId'=>$orderId,
                'user' => $userId,
                'products' => $subOrderProductRepository->findBy(["orderProduct"=>$orderId]),
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
        return $this->render('subOrderProduct/userSubProducts.html.twig', [
            'orderId'=>$orderId,
            'user' => $userId,
            'products' => $subOrderProductRepository->findBy(["orderProduct"=>$orderId, "status"=>$status]),
        ]);
    }

    /**
     * @Route("/admin/changeStatus/", name="subOrderProduct_changeStatus_by_admin", methods={"PUT"})
     * @param Request $request
     * @param SubOrderProductRepository $subOrderProductRepository
     * @return JsonResponse
     */
    public function changeStatusByAdmin(Request $request, SubOrderProductRepository $subOrderProductRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get("id");
            $status= $request->request->get("status");
            $order = $subOrderProductRepository->find($id);
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
}
