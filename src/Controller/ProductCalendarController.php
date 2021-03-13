<?php

namespace App\Controller;

use App\Entity\ProductCalendar;
use App\Form\ProductCalendarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCalendarController extends AbstractController
{
    /**
     * @Route("/product/calendar", name="product_calendar")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $productCalendar = new ProductCalendar();
        $form = $this->createForm(ProductCalendarType::class, $productCalendar);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $productCalendar->setSlots(json_decode($form->get('slots')->getData()));
            dump($productCalendar);
            die();
            $em = $this->getDoctrine()->getManager();
            $em->persist($productCalendar);
            $em->flush();
        }


        return $this->render('product_calendar/index.html.twig', [
            'controller_name' => 'ProductCalendarController',
            'form' => $form->createView(),
        ]);
    }
}

