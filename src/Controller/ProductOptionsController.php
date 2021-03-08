<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductOptions;
use App\Entity\User;
use App\Form\ProductOptionsType;
use App\Repository\ProductOptionsRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
    /**
     * @Route("/{id}/allOptions", name="thisProductOptions", methods={"GET","POST"})
     * @param ProductOptionsRepository $rep
     * @param Product $product
     * @return Response
     */

    public function index(ProductOptionsRepository $rep, Product $product): Response
    {
        return $this->render('product_options/index.html.twig', [
            'controller_name' => 'ProductOptionsController',
            'options' => $rep->findBy(['product' => $product]),
            'product'=>$product
        ]);
    }


    /**
     * @Route("/{id}/new", name="new_option", methods={"GET","POST"})
     * @param ProductOptionsRepository $rep
     * @param Product $product
     * @return Response
     */

    public function newOption( ProductOptionsRepository $rep, Product $product): Response
    {
        $list = [];
        $i=0;
        $options = $rep->findBy(['product' => $product]);
        foreach ($options as $option){
            $list[$i]=$option->getNom();
            $i++;
        }
       // dump($list);die();
        return $this->render('product_options/new.html.twig', [
            'id' => $product,
            'optionNames' => $list,
        ]);
    }


    /**
     * @Route("/list", name="product_options", methods={"GET","POST"})
     * @param Request $request
     * @param ProductOptionsRepository $rep
     * @return JsonResponse
     */
    public function ajaxGetProductsAction(Request $request,ProductRepository $rep): JsonResponse
    {
        $product=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            for($i=0;$i<sizeof($request->request->get('array'));$i++){
                $p = new ProductOptions();
                dump($request->request->get('array')[$i]["nom"]);
                dump($request->request->get('array')[$i]["choices"]);
                dump($request->request->get('array')[$i]["selectedNbChoices"]);
                dump($request->request->get('array')[$i]["product"]);
                $p->setNom($request->request->get('array')[$i]["nom"]);
                $p->setChoices($request->request->get('array')[$i]["choices"]);
                $p->setNbMaxSelected(intval($request->request->get('array')[$i]["selectedNbChoices"]));
                $productId=intval($request->request->get('array')[$i]["product"]);
                $product = $rep->findOneBy(['id'=>$productId]);
                $p->setProduct($product);
                $em->persist($p);
                $em->flush();
            }
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
            return new JsonResponse([
                'success'  => true,
                'redirect' => $this->generateUrl('thisProductOptions',[
                    'id'=> $product,
                ])
            ]);


        }
        return new JsonResponse('no results found', Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/admin/{product}/{option}/delete", name="option_delete", methods={"DELETE"}, requirements={"option"=".+"})
     * @param Request $request
     * @param Product $product
     * @param ProductOptions $option
     * @param ProductOptionsRepository $rep
     * @return Response
     */
    public function delete(Request $request, Product $product,ProductOptions $option, ProductOptionsRepository $rep): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('thisProductOptions',[
            'id'=> $product,
        ],301);
    }
}
