<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceCalendar;
use App\Entity\User;
use App\Form\ServiceCalendarType;
use App\Repository\ServiceCalendarRepository;
use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Time;
/**
 * @Route("/serviceCalendar")
 * @method User|null getUser()
 */
class ServiceCalendarController extends AbstractController
{

    //USER
    /**
     * @Route("/user/service/{id}/", name="service_calendar")
     * @param Request $request
     * @param Service $service
     * @param ServiceCalendarRepository $rep
     * @return Response
     */
    public function index(Request $request, Service $service, ServiceCalendarRepository $rep): Response
    {
        $res = $rep->findOneBy(["service"=>$service]);
        //dump($res);die();
        if($res!==null){
            $serviceCalendar = $res;
            $form = $this->createForm(ServiceCalendarType::class, $serviceCalendar);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $serviceCalendar->setService($service);
                $serviceCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($serviceCalendar);die();
                $em = $this->getDoctrine()->getManager();
                //$em->persist($serviceCalendar);
                $em->flush();
                return $this->redirectToRoute("myServices_new");
            }


            return $this->render('service_calendar/user/edit.html.twig', [
                'controller_name' => 'ServiceCalendarController',
                'form' => $form->createView(),
                'service'=>$service,
                'slots'=>$serviceCalendar->getSlots()
            ]);
        }else{
            $serviceCalendar = new ServiceCalendar();
            $form = $this->createForm(ServiceCalendarType::class, $serviceCalendar);


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $serviceCalendar->setService($service);
                $serviceCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($serviceCalendar);die();
                $em = $this->getDoctrine()->getManager();
                $em->persist($serviceCalendar);
                $em->flush();
                return $this->redirectToRoute("myServices_new");
            }


            return $this->render('service_calendar/user/index.html.twig', [
                'controller_name' => 'ServiceCalendarController',
                'form' => $form->createView(),
                'service'=>$service
            ]);
        }

    }
    //admin

    /**
     * @Route("/admin/user/{user}/service/{id}/", name="admin_service_calendar")
     * @param Request $request
     * @param User $user
     * @param Service $service
     * @param ServiceCalendarRepository $rep
     * @return Response
     */
    public function indexAdmin(Request $request, User $user, Service $service, ServiceCalendarRepository $rep): Response
    {
        $res = $rep->findOneBy(["service"=>$service]);
        //dump($res);die();
        if($res!==null){
            $serviceCalendar = $res;
            $form = $this->createForm(ServiceCalendarType::class, $serviceCalendar);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $serviceCalendar->setService($service);
                $serviceCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($serviceCalendar);die();
                $em = $this->getDoctrine()->getManager();
                //$em->persist($serviceCalendar);
                $em->flush();
                return $this->redirectToRoute("service_new",[
                    "userId"=>$user
                ]);
            }


            return $this->render('service_calendar/edit.html.twig', [
                'controller_name' => 'ServiceCalendarController',
                'form' => $form->createView(),
                'service'=>$service,
                'slots'=>$serviceCalendar->getSlots(),
                'user'=>$user
            ]);
        }else{
            $serviceCalendar = new ServiceCalendar();
            $form = $this->createForm(ServiceCalendarType::class, $serviceCalendar);


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $serviceCalendar->setService($service);
                $serviceCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($serviceCalendar);die();
                $em = $this->getDoctrine()->getManager();
                $em->persist($serviceCalendar);
                $em->flush();
                return $this->redirectToRoute("service_new",[
                    "userId"=>$user
                ]);
            }


            return $this->render('service_calendar/index.html.twig', [
                'controller_name' => 'ServiceCalendarController',
                'form' => $form->createView(),
                'service'=>$service,
                'user'=>$user
            ]);
        }

    }

}

