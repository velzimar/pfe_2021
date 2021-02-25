<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class NotificationController extends AbstractController
{
    /**
     * @Route("/notification", name="notification")
     */
    public function index(): Response
    {
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
        ]);
    }


    /**
     * @Route("/notification/myNotifications", name="myNotifications")
     * @param NotificationRepository $rep
     * @return Response
     */
    public function myNotifications(NotificationRepository $rep): Response
    {
        $notifications = $rep->findByUser($this->getUser());
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications
        ]);
    }
    /**
     * @Route("/notification/makeItSeen", name="notification_is_seen", methods={"GET","POST"})
     * @param Request $request
     * @param NotificationRepository $rep
     * @return Response
     */
    public function makeItSeen(Request $request, NotificationRepository $rep): Response
    {
        $this->addFlash('success', 'here');
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'status' => 'Error',
                'message' => 'Error'),
                400);
        }
        if(isset($request->request)) {
            $notification= $request->request->get('notification');
            $this->addFlash('success', $notification);
            $thisOne = $rep->find($notification);
            $thisOne->setSeen(true);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(array(
                'status' => 'good',
                'message' => 'good'),
                300);
        }
        return new JsonResponse(array(
            'status' => 'Error',
            'message' => 'Error'),
            400);
    }
}
