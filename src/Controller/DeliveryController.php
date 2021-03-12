<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\User;
use App\Form\SelectUserType;
use App\Repository\DeliveryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/delivery")
 * @method User|null getUser()
 */
class DeliveryController extends AbstractController
{

    //ADMIN
    /**
     * @Route("/userDelivery/{id}/", name="new_delivery", methods={"GET","POST"})
     * @param User $user
     * @param DeliveryRepository $deliveryRepository
     * @return Response
     */
    public function newUserOption(User $user, DeliveryRepository $deliveryRepository): Response
    {
        $result = null;
        $result = $deliveryRepository->findOneBy(["user"=>$user]);
        if ($result===null)
        return $this->render('delivery/index.html.twig', [
            'controller_name' => 'DeliveryController',
            'user' => $user
        ]);
        else{
            $array = $result->getLocations();
            $list = [];
            $city = [];
            $i=0;
            foreach ($array as $country){

                $j=0;

                foreach ($country["cities"] as $cityy){

                    $city[$j]=array("name"=> $cityy["name"],"selected"=> ($cityy["selected"]==="true"));
                    $j++;
                }

                $list[$i]=array("country"=> $country["country"], "cities" => $city);
                $i++;
            }
            $info  = array("activation"=> $result->getIsActive()===true, "cout"=>$result->getCost(), "seuil"=>$result->getSeuil());
            /*
            dump($array,$info,$list);
            die();
            */
            return $this->render('delivery/edit.html.twig', [
                'controller_name' => 'DeliveryController',
                'user' => $user,
                'array' => $list,
                'info' => $info,
                'delivery'=>$result
            ]);
        }
    }


    /**
     * @Route("/userDelivery/{id}/response", name="new_delivery_response", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */

    public function userDeliveryResponse(Request $request, User $user): JsonResponse
    {
        $product=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $activation = $request->request->get("info")['activation'];
            $cout = $request->request->get("info")['cout'];
            $seuil = $request->request->get("info")['seuil'];
            $delivery = new Delivery();
            $delivery->setIsActive(($activation==="true")?1:0);
            $delivery->setSeuil(intval($seuil));
            $delivery->setCost(intval($cout));
            $delivery->setUser($user);
            $delivery->setLocations($request->request->get("array"));
            $em->persist($delivery);
            $em->flush();
            return new JsonResponse([
                'success'  => true,

                'redirect' => $this->generateUrl('new_delivery',[
                    'id'=> $user,
                ])

            ]);

        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }

    /**
     * @Route("/userDelivery/{id}/edit/{delivery}/response", name="edit_delivery_response", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param Delivery $delivery
     * @return JsonResponse
     */

    public function userDeliveryResponseEdit(Request $request, User $user,Delivery $delivery): JsonResponse
    {
        $product=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $activation = $request->request->get("info")['activation'];
            $cout = $request->request->get("info")['cout'];
            $seuil = $request->request->get("info")['seuil'];
            $delivery->setIsActive(($activation==="true")?1:0);
            $delivery->setSeuil(intval($seuil));
            $delivery->setCost(intval($cout));
            $delivery->setUser($user);
            $delivery->setLocations($request->request->get("array"));
            $em->flush();
            return new JsonResponse([
                'success'  => true,
                'redirect' => $this->generateUrl('new_delivery',[
                    'id'=> $user,
                ])
            ]);
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }


    /**
     * @Route("/userDelivery/", name="selectUserForDelivery", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function selectUser(Request $request): Response
    {
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->get('id')->getData() != null) {
            $userId = $first->get('id')->getData();
            $this->addFlash('success', "from selectUser $userId");
            return $this->redirectToRoute("new_delivery",[
                'id' => $userId
            ],301);
        }
        return $this->render('delivery/selectUserForDelivery.html.twig', [
            'form' => $first->createView(),
        ]);


    }


    //USER
    /**
     * @Route("/myDelivery/{id}/", name="my_new_delivery", methods={"GET","POST"})
     * @param User $user
     * @param DeliveryRepository $deliveryRepository
     * @return Response
     */
    public function newOption(User $user, DeliveryRepository $deliveryRepository): Response
    {
        $result = null;
        $result = $deliveryRepository->findOneBy(["user"=>$user]);
        if ($result===null)
            return $this->render('delivery/user/index.html.twig', [
                'controller_name' => 'DeliveryController',
                'user' => $user
            ]);
        else{
            $array = $result->getLocations();
            $list = [];
            $city = [];
            $i=0;
            foreach ($array as $country){

                $j=0;

                foreach ($country["cities"] as $cityy){

                    $city[$j]=array("name"=> $cityy["name"],"selected"=> ($cityy["selected"]==="true"));
                    $j++;
                }

                $list[$i]=array("country"=> $country["country"], "cities" => $city);
                $i++;
            }
            $info  = array("activation"=> $result->getIsActive()===true, "cout"=>$result->getCost(), "seuil"=>$result->getSeuil());
            /*
            dump($array,$info,$list);
            die();
            */
            return $this->render('delivery/user/edit.html.twig', [
                'controller_name' => 'DeliveryController',
                'user' => $user,
                'array' => $list,
                'info' => $info,
                'delivery'=>$result
            ]);
        }
    }


    /**
     * @Route("/myDelivery/{id}/response", name="my_new_delivery_response", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */

    public function myDeliveryResponse(Request $request, User $user): JsonResponse
    {
        $product=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $activation = $request->request->get("info")['activation'];
            $cout = $request->request->get("info")['cout'];
            $seuil = $request->request->get("info")['seuil'];
            $delivery = new Delivery();
            $delivery->setIsActive(($activation==="true")?1:0);
            $delivery->setSeuil(intval($seuil));
            $delivery->setCost(intval($cout));
            $delivery->setUser($user);
            $delivery->setLocations($request->request->get("array"));
            $em->persist($delivery);
            $em->flush();
            return new JsonResponse([
                'success'  => true,

                'redirect' => $this->generateUrl('my_new_delivery',[
                    'id'=> $user,
                ])

            ]);

        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }

    /**
     * @Route("/myDelivery/{id}/edit/{delivery}/response", name="my_edit_delivery_response", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param Delivery $delivery
     * @return JsonResponse
     */

    public function myDeliveryResponseEdit(Request $request, User $user,Delivery $delivery): JsonResponse
    {
        $product=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $activation = $request->request->get("info")['activation'];
            $cout = $request->request->get("info")['cout'];
            $seuil = $request->request->get("info")['seuil'];
            $delivery->setIsActive(($activation==="true")?1:0);
            $delivery->setSeuil(intval($seuil));
            $delivery->setCost(intval($cout));
            $delivery->setUser($user);
            $delivery->setLocations($request->request->get("array"));
            $em->flush();
            return new JsonResponse([
                'success'  => true,
                'redirect' => $this->generateUrl('my_new_delivery',[
                    'id'=> $user,
                ])
            ]);
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }




}
