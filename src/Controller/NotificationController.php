<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\ContactType;
use App\Repository\NotificationRepository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/notification")
 * @method User|null getUser()
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/", name="notification")
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
     * @Route("/makeItSeen", name="notification_is_seen", methods={"GET","POST"})
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
        if (isset($request->request)) {
            $notification = $request->request->get('notification');
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


    /**
     * @Route("/user/to_{id}/", name="send_mail_as_seller", methods={"GET","POST"})
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function teamMembers(Swift_Mailer $mailer, Request $request, User $user): Response
    {
        // dump($user->getMainRole());die;
        if ($user->getMainRole() !== "Admin") {
            return $this->render(
                'contact/contact.html.twig'
            );
        }
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        $thisUser = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setSender($thisUser);
            $contact->setReceiver($user);
            $message = (new Swift_Message($contact->getTitle()))
                ->setFrom($contact->getSender()->getEmail())
                ->setTo($contact->getReceiver()->getEmail())
                //->setReplyTo($contact->getEmail())
                ->setBody('<html lang="fr">' .
                    ' <body>' .
                    '  <p>De : <strong>' . $contact->getSender()->getEmail() . "</strong></p><br>" .
                    '  <p>Sujet : <strong>' . $contact->getTitle() . "</strong></p><br>" .
                    '  Message : <br>' . $contact->getMessage() .
                    ' </body>' .
                    '</html>', 'text/html'
                );

            $mailer->send($message);
            $notification = new Notification();
            $notification->setSeen(false);
            $notification->setSender($thisUser);
            $notification->setReceiver($user);
            $notification->setTitle("A envoyé un email");
            $notification->setDetails("Sujet: " . $contact->getTitle());
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();
            return $this->redirectToRoute('send_mail_as_seller', ["id" => $user]);
        }

        return $this->render(
            'contact/contactForm.html.twig',
            array("form" => $form->createView())
        );
    }

    /**
     * @Route("/admin/to_{id}", name="send_mail_as_admin", methods={"GET","POST"})
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function send_as_admin(Swift_Mailer $mailer, Request $request, User $user): Response
    {

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        $thisUser = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setSender($thisUser);
            $contact->setReceiver($user);
            $message = (new Swift_Message($contact->getTitle()))
                ->setFrom($contact->getSender()->getEmail())
                ->setTo($contact->getReceiver()->getEmail())
                //->setReplyTo($contact->getEmail())
                ->setBody('<html lang="fr">' .
                    ' <body>' .
                    '  <p>De : <strong>' . $contact->getSender()->getEmail() . "</strong></p><br>" .
                    '  <p>Sujet : <strong>' . $contact->getTitle() . "</strong></p><br>" .
                    '  Message : <br>' . $contact->getMessage() .
                    ' </body>' .
                    '</html>', 'text/html'
                );

            $mailer->send($message);
            $notification = new Notification();
            $notification->setSeen(false);
            $notification->setSender($thisUser);
            $notification->setReceiver($user);
            $notification->setTitle("A envoyé un email");
            $notification->setDetails("Sujet: " . $contact->getTitle());
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();
            return $this->redirectToRoute('send_mail_as_admin', ["id" => $user]);
        }

        return $this->render(
            'contact/contactForm.html.twig',
            array("form" => $form->createView())
        );
    }

}
