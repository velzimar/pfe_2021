<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\User;
use App\Form\ServiceType;
use App\Form\SelectUserType;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/service")
 * @method User|null getUser()
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("/admin/all", name="service_index", methods={"GET"})
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }


    /**
     * @Route("/admin/selectUser_{action}", name="selectUserForService", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function selectUser(Request $request, $action): Response
    {
        $action = "new";
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->get('id')->getData() != null) {
            $userId = $first->get('id')->getData();
            $this->addFlash('success', "from selectUser $userId");
            return $this->redirectToRoute("service_$action",[
                'userId' => $userId
            ],301);
        }
        return $this->render('service/selectUser.html.twig', [
            'form' => $first->createView(),
        ]);
    }




    /**
     * @Route("/admin/{userId}/new", name="service_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $userId
     * @param ServiceRepository $srep
     * @return Response
     */
    public function new(Request $request, User $userId, ServiceRepository $srep): Response
    {
        $userFound = $srep->findOneBy(["business"=>$userId->getId()]);
        if($userFound!==null){
            return $this->redirectToRoute('service_edit',[
                'userId' => $userId,
                'id' => $userFound
            ],301);
        }
        //echo $userId->getId();
        $service = new Service();
        //$form = $this->createForm(ServiceType::class, $service);
        //$form->handleRequest($request);
        $user = $this->getUser();
        if($userId == "" or $userId == null )
            return $this->redirectToRoute('selectUserForService');
            $this->addFlash('success', "Utilisateur $user");
            $this->addFlash('success', "selected user1 $userId");
            $form = $this->createForm(ServiceType::class, $service,
                ['userId' => $userId //or whatever the variable is called
                    ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
            );
            //$role = $user->getRoles();
            //foreach($role as $rolee){
            //    $this->addFlash('success', "role user: $rolee");
            //}

            $form->handleRequest($request);
            //$service->setBusiness($user);
            $service->setBusiness($userId);
            if ($form->isSubmitted() && $form->isValid()) {
                /*
                if($form->get('imageFile')->getData()==null){

                    $this->addFlash('success', "its null");
                    echo "its null";
                    return $this->render('service/new.html.twig', [
                        'service' => $service,
                        'form' => $form->createView(),
                    ]);
                }
                */
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($service);
                $entityManager->flush();
                return $this->redirectToRoute('service_new',[
                    'userId' => $userId,
                ],301);
            }
        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }


    /**
     * @Route("/myServices/new", name="myServices_new", methods={"GET","POST"})
     * @param Request $request
     * @param ServiceRepository $srep
     * @return Response
     */
    public function myServices_new(Request $request, ServiceRepository $srep): Response
    {
        $userFound = $srep->findOneBy(["business"=>$this->getUser()->getId()]);
        if($userFound!==null){
            return $this->redirectToRoute('myServices_edit',[
                'id' => $userFound
            ],301);
        }
        $service = new Service();
        $user = $this->getUser();
        $this->addFlash('success', "Utilisateur $user");
        $form = $this->createForm(ServiceType::class, $service,
            ['userId' => $user //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );

        $form->handleRequest($request);
        $service->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('myServices_new');
        }
        return $this->render('service/myServices_new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
            'userId'=>$this->getUser()
        ]);
    }





    /**
     * @Route("/admin/{userId}/{id}/edit", name="service_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Service $service
     * @param User $userId
     * @return Response
     */
    public function edit(Request $request, Service $service, User $userId): Response
    {
        //echo $userId->getId();
        //$form = $this->createForm(ServiceType::class, $service);
        //$form->handleRequest($request);
        $user = $this->getUser();

        if($userId == "" or $userId == null )
            return $this->redirectToRoute('selectUserForService');

        $this->addFlash('success', "Utilisateur $user");
        $this->addFlash('success', "selected user1 $userId");
        $form = $this->createForm(ServiceType::class, $service,
            ['userId' => $userId //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')]
        );
        $form->handleRequest($request);
        $service->setBusiness($userId);
        if ($form->isSubmitted() && $form->isValid()) {
/*
            if ($form->get('imageFile')->getData()==null){
                $this->addFlash('success', "its null");

                $service->setImageFile(null);
                $service->setFileName(null);
                $this->getDoctrine()->getManager()->persist($service);
            }
*/
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('service_edit',[
                'userId' => $userId,
                'id'=>$service
            ],301);
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'userId'=> $userId,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/myServices/edit", name="myServices_edit", methods={"GET","POST"})
     * @param Request $request
     * @param ServiceRepository $srep
     * @return Response
     */
    public function myServices_edit(Request $request, ServiceRepository  $srep): Response
    {
        $user = $this->getUser();
        $service = $srep->findOneBy(["business"=>$user]);
        if($service===null){
            return $this->redirectToRoute('myServices_new');
        }
        $this->addFlash('success', "Utilisateur $user");
        $form = $this->createForm(ServiceType::class, $service,
            [
                'userId' => $user //or whatever the variable is called
                ,'userRole'=>$user->hasRole('ROLE_ADMIN')
            ]
        );
        $form->handleRequest($request);
        $service->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            /*
            if ($form->get('imageFile')->getData()==null){
                $this->addFlash('success', "its null");
                $service->setImageFile(null);
                $service->setFileName(null);
                $this->getDoctrine()->getManager()->persist($service);
            }
            */
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('myServices_new');
        }

        return $this->render('service/myServices_edit.html.twig', [
            'service' => $service,
            'userId'=> $user,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("admin/{userId}/{id}/delete", name="service_delete", methods={"DELETE"})
     * @param Request $request
     * @param Service $service
     * @param User $userId
     * @return Response
     */
    public function delete(Request $request, Service $service, User $userId): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_index_user',[
            'userId' => $userId
        ],301);
    }

    /**
     * @Route("/myServices/delete", name="myServices_delete", methods={"DELETE"})
     * @param Request $request
     * @param ServiceRepository $srep
     * @return Response
     */
    public function MyServices_delete(Request $request, ServiceRepository  $srep): Response
    {
        $service = $srep->findOneBy(["business"=>$this->getUser()]);
        //dump($service);die;
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myServices_new');
    }

}
