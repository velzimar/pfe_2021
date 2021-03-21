<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\User;
use App\Entity\WorkingHours;
use App\Form\ServiceType;
use App\Form\SelectUserType;
use App\Form\WorkingHoursType;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Repository\WorkingHoursRepository;
use Spatie\OpeningHours\OpeningHours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/workingHours")
 * @method User|null getUser()
 */
class WorkingHoursController extends AbstractController
{

    /*
     * @Route("/admin/all", name="service_index", methods={"GET"})
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    /*
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }
*/

    /*
     * @Route("/admin/selectUser_{action}", name="selectUserForService", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    /*
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
*/



    /*
     * @Route("/admin/{userId}/new", name="service_new", methods={"GET","POST"})
     * @param Request $request
     * @param User $userId
     * @param ServiceRepository $srep
     * @return Response
     */
    /*
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
*/







    /*
     * @Route("/admin/{userId}/{id}/edit", name="service_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Service $service
     * @param User $userId
     * @return Response
     */
    /*
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
*/

    /*
     * @Route("admin/{userId}/{id}/delete", name="service_delete", methods={"DELETE"})
     * @param Request $request
     * @param Service $service
     * @param User $userId
     * @return Response
     */
    /*
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
    */


    /**
     * @Route("/myWorkingHours/new", name="myWorkingHours_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function myWorkingHours_new(Request $request, WorkingHoursRepository $srep): Response
    {
        $userFound = $srep->findOneBy(["business"=>$this->getUser()->getId()]);
        if($userFound!==null){
            return $this->redirectToRoute('myWorkingHours_edit',[
                'id' => $userFound
            ],301);
        }


/*
        $ranges = [
            'monday' => ['08:00-11:00', '10:00-12:00'],
        ];
        $mergedRanges = OpeningHours::mergeOverlappingRanges($ranges); // Monday becomes ['08:00-12:00']

        OpeningHours::create($mergedRanges);
// Or use the following shortcut to create from ranges that possibly overlap:
        OpeningHours::createAndMergeOverlappingRanges($ranges);
  */
        $workingHours = new WorkingHours();
        $user = $this->getUser();
        $form = $this->createForm(WorkingHoursType::class, $workingHours);

        $form->handleRequest($request);
        $workingHours->setBusiness($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $table = ['monday'=>[null,null],'tuesday'=>[null,null],'wednesday'=>[null,null],'thursday'=>[null,null],'friday'=>[null,null],'saturday'=>[null,null],'sunday'=>[null,null]];

            //init
            foreach($table as $key => $value ){

                //dump($key.'_S1_end');
                $start1 = $key.'_S1_start';
                    $end1=$key.'_S1_end';
                $start2 = $key.'_S2_start';
                $end2=$key.'_S2_end';

                if($form->get($start1)->getData()===null || $form->get($end1)->getData() === null) {
                    $table[$key][0]=null;
                }else{
                    //dump("ok");
                    dump($form->get($start1)->getData()->format('%H:%I')."-".$form->get($end1)->getData()->format('%H:%I'));

                    $table[$key][0] =  $form->get($start1)->getData()->format('%H:%I')."-".$form->get($end1)->getData()->format('%H:%I');
                }
                if($form->get($start2)->getData()===null || $form->get($end2)->getData() === null) {
                    $table[$key][1]=null;
                }else{
                    dump($form->get($start2)->getData()->format('%H:%I')."-".$form->get($end2)->getData()->format('%H:%I'));
                    $table[$key][1] =  $form->get($start2)->getData()->format('%H:%I')."-".$form->get($end2)->getData()->format('%H:%I');
                }

            }
            $ranges = ['monday'=>[],'tuesday'=>[],'wednesday'=>[],'thursday'=>[],'friday'=>[],'saturday'=>[],'sunday'=>[]];

            foreach($table as $key=>$value){
               /*
                dump($table[$key][0]);
                dump($table[$key][1]);
                die;
                */
                if($table[$key][0]!==null)$ranges[$key][0]=$table[$key][0];
                if($table[$key][1]!==null)$ranges[$key][1]=$table[$key][1];
            }
           // die;
/*
            $ranges = [
                'monday' => ['08:00-11:00', '10:00-12:00'],
            ];
            $mergedRanges = OpeningHours::mergeOverlappingRanges($ranges); // Monday becomes ['08:00-12:00']

            OpeningHours::create($mergedRanges);
  */
// Or use the following shortcut to create from ranges that possibly overlap:
            $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
            dump($openingHours->isOpenOn('monday'));
            dump($openingHours->isOpen());

die;








            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($workingHours);
            $entityManager->flush();
            return $this->redirectToRoute('myWorkingHours_new');
        }
        return $this->render('workingHours/myWorkingHours_new.html.twig', [
            'workingHours' => $workingHours,
            'form' => $form->createView(),
            'userId'=>$this->getUser()
        ]);
    }
    /**
     * @Route("/myWorkingHours/{id}/edit", name="myWorkingHours_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Service $service
     * @return Response
     */
    public function myWorkingHours_edit(Request $request, Service $service): Response
    {
        $user = $this->getUser();
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
            return $this->redirectToRoute('myWorkingHours_new');
        }

        return $this->render('service/myWorkingHours_edit.html.twig', [
            'service' => $service,
            'userId'=> $user,
            'form' => $form->createView(),
        ]);
    }





    /**
     * @Route("myWorkingHours/{id}/delete", name="myWorkingHours_delete", methods={"DELETE"})
     * @param Request $request
     * @param Service $service
     * @return Response
     */
    public function MyServices_delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myWorkingHours');
    }

}
