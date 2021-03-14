<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCalendar;
use App\Form\ProductCalendarType;
use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Time;

class ProductCalendarController extends AbstractController
{
    /**
     * @Route("/product/{id}/calendar", name="product_calendar")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function index(Request $request, Product $product): Response
    {

        $productCalendar = new ProductCalendar();
        $form = $this->createForm(ProductCalendarType::class, $productCalendar);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $productCalendar->setProduct($product);
            $productCalendar->setSlots(json_decode($form->get('slots')->getData()));
            $period_string = $form->get('period')->getData().":00";

            dump($period_string);
            $format = 'H:i:s';
            $date = DateTime::createFromFormat($format, $period_string);
            dump($date);
            dump($productCalendar);
            die();
            $productCalendar->setPeriod();
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

