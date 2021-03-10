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
     * @Route("/myOptions/{id}/allOptions", name="thisProductOptions", methods={"GET","POST"})
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
     * @Route("/myOptions/{id}/new", name="new_option", methods={"GET","POST"})
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
     * @Route("/admin/{id}/new", name="new_option_for_admin", methods={"GET","POST"})
     * @param ProductOptionsRepository $rep
     * @param Product $product
     * @return Response
     */
    public function newOptionForAdmin( ProductOptionsRepository $rep, Product $product): Response
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
     * @Route("/myOptions/{product}/{id}/edit", name="edit_this_option", methods={"GET","POST"})
     * @param ProductOptionsRepository $rep
     * @param Product $product
     * @param ProductOptions $product_options
     * @return Response
     */
    public function editOption( ProductOptionsRepository $rep, Product $product, ProductOptions $product_options): Response
    {
        $list = [];
        $i=0;
        $options = $rep->findBy(['product' => $product]);

        $this_option = $rep->findOneBy(['id' => $product_options]);
        foreach ($options as $option){
            if($option->getNom()!=$this_option->getNom()){
                $list[$i]=$option->getNom();
                $i++;
            }
        }
        return $this->render('product_options/edit.html.twig', [
            'id' => $product,
            'option' => $this_option,
            'optionNames' => $list,
            'current_length'=> count($this_option->getChoices())
        ]);
    }

    /**
     * @Route("/list", name="product_options", methods={"GET","POST"})
     * @param Request $request
     * @param ProductOptionsRepository $opRep
     * @param ProductRepository $rep
     * @return JsonResponse
     */
    public function ajaxGetProductsAction(Request $request,ProductOptionsRepository $opRep,ProductRepository $rep): JsonResponse
    {
        $product=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $product = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
            if($request->request->get('array')==[]){
                $product = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('thisProductOptions',[
                        'id'=> $product,
                    ])
                ]);
            }else{
                for($i=0;$i<sizeof($request->request->get('array'));$i++){
                    $op = null;
                    $op = $opRep->findOneBy(['nom'=>$request->request->get('array')[$i]["nom"]]);
                    if($op!=null){
                        continue;
                    }
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
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('thisProductOptions',[
                        'id'=> $product,
                    ])
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }

    /**
     * @Route("/editOption", name="edit_product_options", methods={"GET","POST"})
     * @param Request $request
     * @param ProductOptionsRepository $opRep
     * @param ProductRepository $rep
     * @return JsonResponse
     */
    public function ajaxEditProductsAction(Request $request,ProductOptionsRepository $opRep,ProductRepository $rep): JsonResponse
    {
        $product=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $product = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
            if($request->request->get('array')==[]){
                $product = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
                return new JsonResponse([
                    'empty'  => true,
                    'redirect' => $this->generateUrl('thisProductOptions',[
                        'id'=> $product,
                    ])
                ]);
            }else{
                for($i=0;$i<sizeof($request->request->get('array'));$i++){
                    $p = null;
                    $p = $opRep->findOneBy(['id'=>$request->request->get('option_id')]);
                    if($p==null){
                        continue;
                    }
                    $p->setNom($request->request->get('array')[$i]["nom"]);
                    $p->setChoices($request->request->get('array')[$i]["choices"]);
                    $p->setNbMaxSelected(intval($request->request->get('array')[$i]["selectedNbChoices"]));
                    $productId=intval($request->request->get('array')[$i]["product"]);
                    $product = $rep->findOneBy(['id'=>$productId]);
                    $p->setProduct($product);
                    $em->flush();
                }
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('thisProductOptions',[
                        'id'=> $product,
                    ])
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }

    /**
     * @Route("/myOptions/{product}/{option}/delete", name="option_delete", methods={"DELETE"}, requirements={"option"=".+"})
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
