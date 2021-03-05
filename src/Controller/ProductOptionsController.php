<?php

namespace App\Controller;

use App\Entity\ProductOptions;
use App\Entity\User;
use App\Form\ProductOptionsType;
use App\Repository\ProductOptionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/productOptions")
 * @method User|null getUser()
 */
class ProductOptionsController extends AbstractController
{
    /**
     * @Route("/list", name="product_options")
     * @param ProductOptionsRepository $rep
     * @return Response
     */
    public function index(ProductOptionsRepository $rep): Response
    {
        return $this->render('product_options/index.html.twig', [
            'controller_name' => 'ProductOptionsController',
            'options' => $rep->findAll()
        ]);
    }
    /**
     * @Route("/new", name="new_option", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function newOption(Request $request): Response
    {
        $options = new ProductOptions();
        $form = $this->createForm(ProductOptionsType::class,$options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($options);
            $entityManager->flush();
            return $this->redirectToRoute('product_options');
        }
        return $this->render('product_options/new.html.twig', [
            'product' => $options,
            'form' => $form->createView()
        ]);
    }

}
