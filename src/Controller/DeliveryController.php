<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/delivery")
 * @method User|null getUser()
 */
class DeliveryController extends AbstractController
{
    /**
     * @Route("/myDelivery/{id}/new", name="new_delivery", methods={"GET","POST"})
     * @param User $user
     * @return Response
     */
    public function newOption(User $user): Response
    {

        return $this->render('delivery/index.html.twig', [
            'controller_name' => 'DeliveryController',
            'user' => $user
        ]);
    }




    /*
     * @Route("/list", name="product_options", methods={"GET","POST"})
     * @param Request $request
     * @param ProductOptionsRepository $opRep
     * @param ProductRepository $rep
     * @return JsonResponse
     */
    /*
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
*/
}
