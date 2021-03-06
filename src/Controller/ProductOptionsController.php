<?php

namespace App\Controller;

use App\Entity\ProductOptions;
use App\Entity\User;
use App\Form\ProductOptionsType;
use App\Repository\ProductOptionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/productOptions")
 * @method User|null getUser()
 */
class ProductOptionsController extends AbstractController
{
    /*
     * @Route("/list", name="product_options")
     * @param ProductOptionsRepository $rep
     * @return Response
     */
    /*
    public function index(ProductOptionsRepository $rep): Response
    {
        return $this->render('product_options/index.html.twig', [
            'controller_name' => 'ProductOptionsController',
            'options' => $rep->findAll()
        ]);
    }
*/

    /**
     * @Route("/new", name="new_option", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */

    public function newOption(Request $request): Response
    {
        $options = new ProductOptions();
        /*
        $form = $this->createForm(ProductOptionsType::class,$options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($options);
            $entityManager->flush();
            return $this->redirectToRoute('product_options');
        }
        */
        return $this->render('product_options/new.html.twig', [
            //'product' => $options,
            //'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/list", name="product_options", methods={"GET","POST"})
     * @param Request $request
     * @param ProductOptionsRepository $rep
     */
    public function ajaxGetProductsAction(Request $request,ProductOptionsRepository $rep): JsonResponse
    {
        $output=array();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $p = new ProductOptions();
            $p->setNom("test");
            $p->setChoices(["test"]);
            $em->persist($p);
            $em->flush();
            dump("aaa");
            /*
            $themes = $em->getRepository('WebSiteBackBundle:theme');
            $themes = $themes->findAll();
            foreach ($themes as $theme){

                $output[]=array($theme->getId(),$theme->getName());
            }
               var_dump($themes);
               $json = json_encode($themes);

               $response = new Response();*/
            //            return $response->setContent($json);
            return new JsonResponse($output);

        }
        return new JsonResponse('no results found', Response::HTTP_NOT_FOUND);
    }
    /*
    {
        echo "aaa";
        return $this->render('product_options/index.html.twig', [
            'controller_name' => 'ProductOptionsController',
            'options' => $rep->findAll()
        ]);

        // This is optional.
        // Only include it if the function is reserved for ajax calls only.


        if(isset($request->request))
        {

            $template_id = $request->request->get('array');
            $template_id = intval($template_id);
            dump("heeeeeeeeeeeeeeeeeere");
            dump($template_id);die();

            if ($template_id == 0)
            {
                // You can customize error messages
                // However keep in mind that this data is visible client-side
                // You shouldn't give out clues to what went wrong to potential attackers
                return new JsonResponse(array(
                    'status' => 'Error',
                    'message' => 'Error'),
                    400);
            }

            // Check that the template object really exists and fetch it


            $templateRepository = $entityManager->getRepository('PinkGeekBundle:Template');
            $template = $templateRepository->findOneBy(array(
                'id' => $template_id
            ));


        }


    }*/
}
