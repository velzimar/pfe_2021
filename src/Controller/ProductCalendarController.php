<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductCalendar;
use App\Entity\User;
use App\Form\ProductCalendarType;
use App\Repository\ProductCalendarRepository;
use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Time;
/**
 * @Route("/productCalendar")
 * @method User|null getUser()
 */
class ProductCalendarController extends AbstractController
{

    //USER
    /**
     * @Route("/user/product/{id}/", name="product_calendar")
     * @param Request $request
     * @param Product $product
     * @param ProductCalendarRepository $rep
     * @return Response
     */
    public function index(Request $request, Product $product, ProductCalendarRepository $rep): Response
    {
        $res = $rep->findOneBy(["product"=>$product]);
        //dump($res);die();
        if($res!==null){
            $productCalendar = $res;
            $form = $this->createForm(ProductCalendarType::class, $productCalendar);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $productCalendar->setProduct($product);
                $productCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($productCalendar);die();
                $em = $this->getDoctrine()->getManager();
                //$em->persist($productCalendar);
                $em->flush();
                return $this->redirectToRoute("myProducts");
            }


            return $this->render('product_calendar/user/edit.html.twig', [
                'controller_name' => 'ProductCalendarController',
                'form' => $form->createView(),
                'product'=>$product,
                'slots'=>$productCalendar->getSlots()
            ]);
        }else{
            $productCalendar = new ProductCalendar();
            $form = $this->createForm(ProductCalendarType::class, $productCalendar);


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $productCalendar->setProduct($product);
                $productCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($productCalendar);die();
                $em = $this->getDoctrine()->getManager();
                $em->persist($productCalendar);
                $em->flush();
                return $this->redirectToRoute("myProducts");
            }


            return $this->render('product_calendar/user/index.html.twig', [
                'controller_name' => 'ProductCalendarController',
                'form' => $form->createView(),
                'product'=>$product
            ]);
        }

    }
    //admin

    /**
     * @Route("/admin/user/{user}/product/{id}/", name="admin_product_calendar")
     * @param Request $request
     * @param User $user
     * @param Product $product
     * @param ProductCalendarRepository $rep
     * @return Response
     */
    public function indexAdmin(Request $request, User $user, Product $product, ProductCalendarRepository $rep): Response
    {
        $res = $rep->findOneBy(["product"=>$product]);
        //dump($res);die();
        if($res!==null){
            $productCalendar = $res;
            $form = $this->createForm(ProductCalendarType::class, $productCalendar);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $productCalendar->setProduct($product);
                $productCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($productCalendar);die();
                $em = $this->getDoctrine()->getManager();
                //$em->persist($productCalendar);
                $em->flush();
                return $this->redirectToRoute("product_index_user",[
                    "userId"=>$user
                ]);
            }


            return $this->render('product_calendar/edit.html.twig', [
                'controller_name' => 'ProductCalendarController',
                'form' => $form->createView(),
                'product'=>$product,
                'slots'=>$productCalendar->getSlots(),
                'user'=>$user
            ]);
        }else{
            $productCalendar = new ProductCalendar();
            $form = $this->createForm(ProductCalendarType::class, $productCalendar);


            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $productCalendar->setProduct($product);
                $productCalendar->setSlots(json_decode($form->get('slots')->getData()));
                //  dump($productCalendar);die();
                $em = $this->getDoctrine()->getManager();
                $em->persist($productCalendar);
                $em->flush();
                return $this->redirectToRoute("product_index_user",[
                    "userId"=>$user
                ]);
            }


            return $this->render('product_calendar/index.html.twig', [
                'controller_name' => 'ProductCalendarController',
                'form' => $form->createView(),
                'product'=>$product,
                'user'=>$user
            ]);
        }

    }

}

