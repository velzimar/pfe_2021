<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceOptions;
use App\Entity\User;
use App\Form\ServiceOptionsType;
use App\Repository\ServiceOptionsRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/serviceOptions")
 * @method User|null getUser()
 */
class ServiceOptionsController extends AbstractController
{
    /**
     * @Route("/myOptions/{id}/allOptions", name="thisServiceOptions", methods={"GET","POST"})
     * @param ServiceOptionsRepository $rep
     * @param Service $service
     * @return Response
     */

    public function index(ServiceOptionsRepository $rep, Service $service): Response
    {
        $options = $rep->findBy(['service' => $service]);

        return $this->render('service_options/index.html.twig', [
            'controller_name' => 'ServiceOptionsController',
            'options' => $options,
            'product'=>$service
        ]);
    }

    /**
     * @Route("/admin/user_{user}/service_{id}/allOptions", name="userServiceOptions", methods={"GET","POST"})
     * @param ServiceOptionsRepository $rep
     * @param User $user
     * @param Service $service
     * @return Response
     */

    public function userServiceOption(ServiceOptionsRepository $rep, User $user,Service $service): Response
    {
        return $this->render('service_options/admin/index.html.twig', [
            'controller_name' => 'ServiceOptionsController',
            'options' => $rep->findBy(['service' => $service]),
            'product'=>$service,
            'user'=>$user
        ]);
    }

    /**
     * @Route("/myOptions/{id}/new", name="new_option_service", methods={"GET","POST"})
     * @param Service $service
     * @return Response
     */
    public function newOption(Service $service): Response
    {
        /*
        $list = [];
        $i=0;
        $options = $rep->findBy(['service' => $service]);
        foreach ($options as $option){
            $list[$i]=$option->getNom();
            $i++;
        }
        */
        // dump($list);die();
        return $this->render('service_options/new.html.twig', [
            'id' => $service,
            // 'optionNames' => $list,
        ]);
    }


    /**
     * @Route("/admin/user_{user}/service_{id}/new", name="user_new_option_service", methods={"GET","POST"})
     * @param User $user
     * @param Service $service
     * @return Response
     */
    public function userNewOption(User $user ,Service $service): Response
    {
        /*
        $list = [];
        $i=0;
        $options = $rep->findBy(['service' => $service]);
        foreach ($options as $option){
            $list[$i]=$option->getNom();
            $i++;
        }
        */
        // dump($list);die();
        return $this->render('service_options/admin/new.html.twig', [
            'id' => $service,
            // 'optionNames' => $list,
            'user' => $user
        ]);
    }


    /**
     * @Route("/admin/{id}/new", name="new_option_for_admin_service", methods={"GET","POST"})
     * @param Service $service
     * @return Response
     */
    public function newOptionForAdmin(Service $service): Response
    {
        /*
        $list = [];
        $i=0;
        $options = $rep->findBy(['service' => $service]);
        foreach ($options as $option){
            $list[$i]=$option->getNom();
            $i++;
        }
        */
        // dump($list);die();
        return $this->render('service_options/new.html.twig', [
            'id' => $service,
            //  'optionNames' => $list,
        ]);
    }


    /**
     * @Route("/myOptions/{service}/{id}/edit", name="edit_this_option_service", methods={"GET","POST"})
     * @param ServiceOptionsRepository $rep
     * @param Service $service
     * @param ServiceOptions $service_options
     * @return Response
     */
    public function editOption( ServiceOptionsRepository $rep, Service $service, ServiceOptions $service_options): Response
    {
        /*
        $list = [];
        $i=0;
        $options = $rep->findBy(['service' => $service]);
*/
        $this_option = $rep->findOneBy(['id' => $service_options]);
        /*
        foreach ($options as $option){
            if($option->getNom()!=$this_option->getNom()){
                $list[$i]=$option->getNom();
                $i++;
            }
        }
        */
        return $this->render('service_options/edit.html.twig', [
            'id' => $service,
            'option' => $this_option,
            //'optionNames' => $list,
            'current_length'=> count($this_option->getChoices())
        ]);
    }

    /**
     * @Route("/admin/user_{user}/service_{service}/option_{id}/edit", name="edit_user_option_service", methods={"GET","POST"})
     * @param ServiceOptionsRepository $rep
     * @param User $user
     * @param Service $service
     * @param ServiceOptions $service_options
     * @return Response
     */
    public function userEditOption( ServiceOptionsRepository $rep, User  $user,Service $service, ServiceOptions $service_options): Response
    {

        $this_option = $rep->findOneBy(['id' => $service_options]);
        /*
        $list = [];
        $i=0;
        $options = $rep->findBy(['service' => $service]);

        foreach ($options as $option){
            if($option->getNom()!=$this_option->getNom()){
                $list[$i]=$option->getNom();
                $i++;
            }
        }
        */
        /*
        dump($service);
        dump($this_option);
        dump($list);
        dump($user);
        dump(count($this_option->getChoices()));
            die();
        */
        return $this->render('service_options/admin/edit.html.twig', [
            'id' => $service,
            'option' => $this_option,
            // 'optionNames' => $list,
            'current_length'=> count($this_option->getChoices()),
            'user' => $user
        ]);
    }


    /**
     * @Route("/list", name="service_options", methods={"GET","POST"})
     * @param Request $request
     * @param ServiceOptionsRepository $opRep
     * @param ServiceRepository $rep
     * @return JsonResponse
     */
    public function ajaxGetServicesAction(Request $request,ServiceOptionsRepository $opRep,ServiceRepository $rep): JsonResponse
    {
        $service=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
            if($request->request->get('array')==[]){
                $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('thisServiceOptions',[
                        'id'=> $service,
                    ])
                ]);
            }else{
                for($i=0;$i<sizeof($request->request->get('array'));$i++){
                    $op = null;
                    $op = $opRep->findOneBy(['nom'=>$request->request->get('array')[$i]["nom"],'service'=>$service->getId()]);
                    if($op!=null){
                        continue;
                    }
                    $p = new ServiceOptions();
                    dump($request->request->get('array')[$i]["nom"]);
                    dump($request->request->get('array')[$i]["choices"]);
                    dump($request->request->get('array')[$i]["selectedNbChoices"]);
                    dump($request->request->get('array')[$i]["product"]);
                    $p->setNom($request->request->get('array')[$i]["nom"]);
                    $p->setChoices($request->request->get('array')[$i]["choices"]);
                    $p->setNbMaxSelected(intval($request->request->get('array')[$i]["selectedNbChoices"]));
                    $serviceId=intval($request->request->get('array')[$i]["product"]);
                    $service = $rep->findOneBy(['id'=>$serviceId]);
                    $p->setService($service);
                    $em->persist($p);
                    $em->flush();
                }
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('thisServiceOptions',[
                        'id'=> $service,
                    ])
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }

    /**
     * @Route("/admin/new", name="user_new_service_options", methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $userRep
     * @param ServiceOptionsRepository $opRep
     * @param ServiceRepository $rep
     * @return JsonResponse
     */
    public function userAjaxGetServicesAction(Request $request,UserRepository $userRep,ServiceOptionsRepository $opRep,ServiceRepository $rep): JsonResponse
    {
        $service=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
            $user = $userRep->findOneBy(['id'=>$request->request->get('user_id')]);
            // dump($user);die();
            if($request->request->get('array')==[]){
                $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('userServiceOptions',[
                        'id'=> $service,
                        'user'=> $user
                    ])
                ]);
            }else{
                for($i=0;$i<sizeof($request->request->get('array'));$i++){
                    $op = null;
                    $op = $opRep->findOneBy(['nom'=>$request->request->get('array')[$i]["nom"],'service'=>$service->getId()]);
                    if($op!=null){
                        continue;
                    }
                    $p = new ServiceOptions();
                    dump($request->request->get('array')[$i]["nom"]);
                    dump($request->request->get('array')[$i]["choices"]);
                    dump($request->request->get('array')[$i]["selectedNbChoices"]);
                    dump($request->request->get('array')[$i]["product"]);
                    $p->setNom($request->request->get('array')[$i]["nom"]);
                    $p->setChoices($request->request->get('array')[$i]["choices"]);
                    $p->setNbMaxSelected(intval($request->request->get('array')[$i]["selectedNbChoices"]));
                    $serviceId=intval($request->request->get('array')[$i]["product"]);
                    $service = $rep->findOneBy(['id'=>$serviceId]);
                    $p->setService($service);
                    $em->persist($p);
                    $em->flush();
                }
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('userServiceOptions',[
                        'id'=> $service,
                        'user'=> $user
                    ])
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }


    /**
     * @Route("/checkUnique/", name="admin_check_unique_service", methods={"GET","POST"})
     * @param Request $request
     * @param ServiceOptionsRepository $opRep
     * @param ServiceRepository $rep
     * @return JsonResponse
     */
    public function admin_check_unique(Request $request,ServiceOptionsRepository $opRep,ServiceRepository $rep): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $thisOption = null;
            if($request->request->has('option_id')){
                $thisOption = $opRep->find(['id'=>$request->request->get('option_id')]);
                //$thisOptionName = $thisOption->getNom();
            }
            $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
            $option_name = $request->request->get('option_name');
            // dump($user);die();
            $res = $opRep->findOneBy(['nom'=>$option_name,'service'=>$service->getId()]);
            return new JsonResponse([
                'success'  => $res===null || $res===$thisOption,
            ]);
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }


    /**
     * @Route("/editOption", name="edit_service_options", methods={"GET","POST"})
     * @param Request $request
     * @param ServiceOptionsRepository $opRep
     * @param ServiceRepository $rep
     * @return JsonResponse
     */
    public function ajaxEditServicesAction(Request $request,ServiceOptionsRepository $opRep,ServiceRepository $rep): JsonResponse
    {
        $service=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
            if($request->request->get('array')==[]){
                $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
                return new JsonResponse([
                    'empty'  => true,
                    'redirect' => $this->generateUrl('thisServiceOptions',[
                        'id'=> $service,
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
                    $serviceId=intval($request->request->get('array')[$i]["product"]);
                    $service = $rep->findOneBy(['id'=>$serviceId]);
                    $p->setService($service);
                    $em->flush();
                }
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('thisServiceOptions',[
                        'id'=> $service,
                    ])
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }


    /**
     * @Route("/admin/edit", name="user_edit_service_options", methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $userRep
     * @param ServiceOptionsRepository $opRep
     * @param ServiceRepository $rep
     * @return JsonResponse
     */
    public function ajaxUserEditServicesAction(Request $request,UserRepository $userRep,ServiceOptionsRepository $opRep,ServiceRepository $rep): JsonResponse
    {
        $service=null;
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $user = $userRep->findOneBy(['id'=>$request->request->get('user_id')]);
            $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
            if($request->request->get('array')==[]){
                $service = $rep->findOneBy(['id'=>$request->request->get('product_id')]);
                return new JsonResponse([
                    'empty'  => true,
                    'redirect' => $this->generateUrl('userServiceOptions',[
                        'id'=> $service,
                        'user' => $user
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
                    $serviceId=intval($request->request->get('array')[$i]["product"]);
                    $service = $rep->findOneBy(['id'=>$serviceId]);
                    $p->setService($service);
                    $em->flush();
                }
                return new JsonResponse([
                    'success'  => true,
                    'redirect' => $this->generateUrl('userServiceOptions',[
                        'id'=> $service,
                        'user' => $user
                    ])
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }




    /**
     * @Route("/myOptions/{service}/{option}/delete", name="option_delete_service", methods={"DELETE"}, requirements={"option"=".+"})
     * @param Request $request
     * @param Service $service
     * @param ServiceOptions $option
     * @param ServiceOptionsRepository $rep
     * @return Response
     */
    public function delete(Request $request, Service $service,ServiceOptions $option, ServiceOptionsRepository $rep): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('thisServiceOptions',[
            'id'=> $service,
        ],301);
    }


    /**
     * @Route("/admin/{user}/{service}/{option}/delete", name="user_option_delete_service", methods={"DELETE"}, requirements={"option"=".+"})
     * @param Request $request
     * @param User $user
     * @param Service $service
     * @param ServiceOptions $option
     * @return Response
     */
    public function userOptionDelete(Request $request,User $user, Service $service,ServiceOptions $option): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('userServiceOptions',[
            'id'=> $service,
            'user' => $user
        ],301);
    }
}
