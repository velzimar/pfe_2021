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
use DateTime;
use Spatie\OpeningHours\OpeningHours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

//as USER
    /**
     * @Route("/myWorkingHours/new", name="myWorkingHours_new", methods={"GET","POST"})
     * @param Request $request
     * @param WorkingHoursRepository $srep
     * @return Response
     */
    public function myWorkingHours_new(Request $request, WorkingHoursRepository $srep, ServiceRepository $serviceRepository): Response
    {
        $hasService = $serviceRepository->findOneBy(["business"=>$this->getUser()->getId()]);

        $table = ['monday' => [null, null], 'tuesday' => [null, null], 'wednesday' => [null, null], 'thursday' => [null, null], 'friday' => [null, null], 'saturday' => [null, null], 'sunday' => [null, null]];
        $userFound = $srep->findOneBy(["business" => $this->getUser()->getId()]);

        if ($userFound !== null) {
            $form = $this->createForm(WorkingHoursType::class, $userFound);
            $hours = $userFound->getHours();
            dump($hours);

            foreach($hours as $key=>$val){
                if(array_key_exists(0, $hours[$key]))  $table[$key][0]=$hours[$key][0];
                if(array_key_exists(1, $hours[$key]))   $table[$key][1]=$hours[$key][1];
            }
            dump($table);
         //  die;

            foreach($table as $key=>$val){
               // working_hours[monday_S1_start][hours]
                    $start1 = $key . '_S1_start';
                    $end1 = $key . '_S1_end';

                    $start2 =$key . '_S2_start';
                    $end2 = $key . '_S2_end';



                if(array_key_exists(0, $hours[$key]))
                {

                    $form->get($start1)["minutes"]->setData(intval(substr($table[$key][0],3,2)));
                    $form->get($start1)["hours"]->setData(intval(substr($table[$key][0],0,2)));

                    $form->get($end1)["minutes"]->setData(intval(substr($table[$key][0],9,2)));
                    $form->get($end1)["hours"]->setData(intval(substr($table[$key][0],6,2)));
                }

                if(array_key_exists(1, $hours[$key]))   {

                    $form->get($start2)["minutes"]->setData(intval(substr($table[$key][1],3,2)));
                    $form->get($start2)["hours"]->setData(intval(substr($table[$key][1],0,2)));

                    $form->get($end2)["minutes"]->setData(intval(substr($table[$key][1],9,2)));
                    $form->get($end2)["hours"]->setData(intval(substr($table[$key][1],6,2)));
                }


            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $table = ['monday' => [null, null], 'tuesday' => [null, null], 'wednesday' => [null, null], 'thursday' => [null, null], 'friday' => [null, null], 'saturday' => [null, null], 'sunday' => [null, null]];

                //init
                foreach ($table as $key => $value) {

                    //dump($key.'_S1_end');
                    $start1 = $key . '_S1_start';
                    $end1 = $key . '_S1_end';
                    $start2 = $key . '_S2_start';
                    $end2 = $key . '_S2_end';

                    if ($form->get($start1)->getData() === null || $form->get($end1)->getData() === null) {
                        $table[$key][0] = null;
                    } else {
                        //dump("ok");
                        dump($form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I'));

                        $table[$key][0] = $form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I');
                    }
                    if ($form->get($start2)->getData() === null || $form->get($end2)->getData() === null) {
                        $table[$key][1] = null;
                    } else {
                        dump($form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I'));
                        $table[$key][1] = $form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I');
                    }

                }
                $ranges = ['monday' => [], 'tuesday' => [], 'wednesday' => [], 'thursday' => [], 'friday' => [], 'saturday' => [], 'sunday' => []];

                foreach ($table as $key => $value) {

                    if ($table[$key][0] !== null) $ranges[$key][0] = $table[$key][0];
                    if ($table[$key][1] !== null) $ranges[$key][1] = $table[$key][1];
                }

                dump($ranges);
                $userFound->setHours($ranges);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return $this->redirectToRoute('myWorkingHours_new',['service'=>$hasService]);
            }

        } else

            {
            $workingHours = new WorkingHours();
            $user = $this->getUser();
            $form = $this->createForm(WorkingHoursType::class, $workingHours);

            $form->handleRequest($request);
            $workingHours->setBusiness($user);
            if ($form->isSubmitted() && $form->isValid()) {
                $table = ['monday' => [null, null], 'tuesday' => [null, null], 'wednesday' => [null, null], 'thursday' => [null, null], 'friday' => [null, null], 'saturday' => [null, null], 'sunday' => [null, null]];

                //init
                foreach ($table as $key => $value) {

                    //dump($key.'_S1_end');
                    $start1 = $key . '_S1_start';
                    $end1 = $key . '_S1_end';
                    $start2 = $key . '_S2_start';
                    $end2 = $key . '_S2_end';

                    if ($form->get($start1)->getData() === null || $form->get($end1)->getData() === null) {
                        $table[$key][0] = null;
                    } else {
                        //dump("ok");
                        dump($form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I'));

                        $table[$key][0] = $form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I');
                    }
                    if ($form->get($start2)->getData() === null || $form->get($end2)->getData() === null) {
                        $table[$key][1] = null;
                    } else {
                        dump($form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I'));
                        $table[$key][1] = $form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I');
                    }

                }
                $ranges = ['monday' => [], 'tuesday' => [], 'wednesday' => [], 'thursday' => [], 'friday' => [], 'saturday' => [], 'sunday' => []];

                foreach ($table as $key => $value) {
                    /*
                     dump($table[$key][0]);
                     dump($table[$key][1]);
                     die;
                     */
                    if ($table[$key][0] !== null) $ranges[$key][0] = $table[$key][0];
                    if ($table[$key][1] !== null) $ranges[$key][1] = $table[$key][1];
                }
                /*
                            $ranges = [
                                'monday' => ['08:00-11:00', '10:00-12:00'],
                            ];
                            $mergedRanges = OpeningHours::mergeOverlappingRanges($ranges); // Monday becomes ['08:00-12:00']

                            OpeningHours::create($mergedRanges);
                  */
                /*
                 $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
                 dump($openingHours->isOpenOn('monday'));
                 dump($openingHours->isOpen());
     */



                $workingHours->setHours($ranges);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($workingHours);
                $entityManager->flush();
                return $this->redirectToRoute('myWorkingHours_new',['service'=>$hasService]);
            }
        }

        return $this->render('workingHours/myWorkingHours_new.html.twig', [
            'service'=>$hasService,
            'form' => $form->createView(),
            'userId' => $this->getUser()
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
        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myWorkingHours');
    }


    /**
     * @Route("/test", name="testttt")
     * @param Request $request
     * @param WorkingHoursRepository $rep
     * @return JsonResponse
     * @throws \Exception
     */
    public function test(Request $request, WorkingHoursRepository $rep): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            if($request->request->get('start')===null && $request->request->get('end')===null){
                return new JsonResponse([
                    'success'  => false,
                ]);
            }else{
                $workingHours = $rep->findOneBy(["business"=>$this->getUser()]);
                $test = $workingHours->getHours();
                //dump($test);
                $mergedRanges = OpeningHours::mergeOverlappingRanges($test);
                $openingHours= OpeningHours::create($mergedRanges);
               // $day =$request->request->get('day');
                $start = $request->request->get('start');
                $end = $request->request->get('end');
                dump($start);
                dump($end);
               // die;
                $res = $openingHours->diffInOpenMinutes(new DateTime($start), new DateTime($end));


                $checkTime = strtotime($start);
                $loginTime = strtotime($end);
                $diff =  ($loginTime - $checkTime)/60 ;
                dump($diff);
                dump($res);
                dump($diff<=$res);
                //dump($day);
                return new JsonResponse([
                    'success'  => true,
                    'result' => $diff<=$res
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }
    //as admin
    /**
     * @Route("/admin/selectUser", name="selectUser_for_workingHours", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function selectUser(Request $request): Response
    {
        $first = $this->createForm(SelectUserType::class);
        $first->handleRequest($request);
        if ($first->isSubmitted() && $first->get('id')->getData() != null) {
            $userId = $first->get('id')->getData();
            return $this->redirectToRoute("myWorkingHours_new_admin",[
                'id' => $userId
            ],301);
        }
        return $this->render('workingHours/selectUser.html.twig', [
            'form' => $first->createView(),
        ]);
    }

    /**
     * @Route("/admin/user_{id}/", name="myWorkingHours_new_admin", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param WorkingHoursRepository $srep
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function admin_new(Request $request, User $user,WorkingHoursRepository $srep, ServiceRepository $serviceRepository): Response
    {
        $hasService = $serviceRepository->findOneBy(["business"=>$user]);

        $table = ['monday' => [null, null], 'tuesday' => [null, null], 'wednesday' => [null, null], 'thursday' => [null, null], 'friday' => [null, null], 'saturday' => [null, null], 'sunday' => [null, null]];
        $userFound = $srep->findOneBy(["business" => $user]);

        if ($userFound !== null) {
            $form = $this->createForm(WorkingHoursType::class, $userFound);
            $hours = $userFound->getHours();
            dump($hours);

            foreach($hours as $key=>$val){
                if(array_key_exists(0, $hours[$key]))  $table[$key][0]=$hours[$key][0];
                if(array_key_exists(1, $hours[$key]))   $table[$key][1]=$hours[$key][1];
            }
            dump($table);
            //  die;

            foreach($table as $key=>$val){
                // working_hours[monday_S1_start][hours]
                $start1 = $key . '_S1_start';
                $end1 = $key . '_S1_end';

                $start2 =$key . '_S2_start';
                $end2 = $key . '_S2_end';



                if(array_key_exists(0, $hours[$key]))
                {

                    $form->get($start1)["minutes"]->setData(intval(substr($table[$key][0],3,2)));
                    $form->get($start1)["hours"]->setData(intval(substr($table[$key][0],0,2)));

                    $form->get($end1)["minutes"]->setData(intval(substr($table[$key][0],9,2)));
                    $form->get($end1)["hours"]->setData(intval(substr($table[$key][0],6,2)));
                }

                if(array_key_exists(1, $hours[$key]))   {

                    $form->get($start2)["minutes"]->setData(intval(substr($table[$key][1],3,2)));
                    $form->get($start2)["hours"]->setData(intval(substr($table[$key][1],0,2)));

                    $form->get($end2)["minutes"]->setData(intval(substr($table[$key][1],9,2)));
                    $form->get($end2)["hours"]->setData(intval(substr($table[$key][1],6,2)));
                }


            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $table = ['monday' => [null, null], 'tuesday' => [null, null], 'wednesday' => [null, null], 'thursday' => [null, null], 'friday' => [null, null], 'saturday' => [null, null], 'sunday' => [null, null]];

                //init
                foreach ($table as $key => $value) {

                    //dump($key.'_S1_end');
                    $start1 = $key . '_S1_start';
                    $end1 = $key . '_S1_end';
                    $start2 = $key . '_S2_start';
                    $end2 = $key . '_S2_end';

                    if ($form->get($start1)->getData() === null || $form->get($end1)->getData() === null) {
                        $table[$key][0] = null;
                    } else {
                        //dump("ok");
                        dump($form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I'));

                        $table[$key][0] = $form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I');
                    }
                    if ($form->get($start2)->getData() === null || $form->get($end2)->getData() === null) {
                        $table[$key][1] = null;
                    } else {
                        dump($form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I'));
                        $table[$key][1] = $form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I');
                    }

                }
                $ranges = ['monday' => [], 'tuesday' => [], 'wednesday' => [], 'thursday' => [], 'friday' => [], 'saturday' => [], 'sunday' => []];

                foreach ($table as $key => $value) {

                    if ($table[$key][0] !== null) $ranges[$key][0] = $table[$key][0];
                    if ($table[$key][1] !== null) $ranges[$key][1] = $table[$key][1];
                }

                dump($ranges);
                $userFound->setHours($ranges);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return $this->redirectToRoute('myWorkingHours_new_admin',['service'=>$hasService, 'id' => $user]);
            }

        } else

        {
            $workingHours = new WorkingHours();
            $form = $this->createForm(WorkingHoursType::class, $workingHours);

            $form->handleRequest($request);
            $workingHours->setBusiness($user);
            if ($form->isSubmitted() && $form->isValid()) {
                $table = ['monday' => [null, null], 'tuesday' => [null, null], 'wednesday' => [null, null], 'thursday' => [null, null], 'friday' => [null, null], 'saturday' => [null, null], 'sunday' => [null, null]];

                //init
                foreach ($table as $key => $value) {

                    //dump($key.'_S1_end');
                    $start1 = $key . '_S1_start';
                    $end1 = $key . '_S1_end';
                    $start2 = $key . '_S2_start';
                    $end2 = $key . '_S2_end';

                    if ($form->get($start1)->getData() === null || $form->get($end1)->getData() === null) {
                        $table[$key][0] = null;
                    } else {
                        //dump("ok");
                        dump($form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I'));

                        $table[$key][0] = $form->get($start1)->getData()->format('%H:%I') . "-" . $form->get($end1)->getData()->format('%H:%I');
                    }
                    if ($form->get($start2)->getData() === null || $form->get($end2)->getData() === null) {
                        $table[$key][1] = null;
                    } else {
                        dump($form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I'));
                        $table[$key][1] = $form->get($start2)->getData()->format('%H:%I') . "-" . $form->get($end2)->getData()->format('%H:%I');
                    }

                }
                $ranges = ['monday' => [], 'tuesday' => [], 'wednesday' => [], 'thursday' => [], 'friday' => [], 'saturday' => [], 'sunday' => []];

                foreach ($table as $key => $value) {
                    /*
                     dump($table[$key][0]);
                     dump($table[$key][1]);
                     die;
                     */
                    if ($table[$key][0] !== null) $ranges[$key][0] = $table[$key][0];
                    if ($table[$key][1] !== null) $ranges[$key][1] = $table[$key][1];
                }
                /*
                            $ranges = [
                                'monday' => ['08:00-11:00', '10:00-12:00'],
                            ];
                            $mergedRanges = OpeningHours::mergeOverlappingRanges($ranges); // Monday becomes ['08:00-12:00']

                            OpeningHours::create($mergedRanges);
                  */
                /*
                 $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
                 dump($openingHours->isOpenOn('monday'));
                 dump($openingHours->isOpen());
     */



                $workingHours->setHours($ranges);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($workingHours);
                $entityManager->flush();
                return $this->redirectToRoute('myWorkingHours_new_admin',['service'=>$hasService, 'id' => $user]);
            }
        }

        return $this->render('workingHours/userWorkingHours_new.html.twig', [
            'service'=>$hasService,
            'form' => $form->createView(),
            'userId' => $user
        ]);
    }


    /**
     * @Route("/admin/{id}/delete", name="myWorkingHours_delete_admin", methods={"DELETE"})
     * @param Request $request
     * @param Service $service
     * @return Response
     */
    public function admin_delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }
        return $this->redirectToRoute('myWorkingHours');
    }


    /**
     * @Route("/admin/user_{id}/test", name="testttte")
     * @param User $user
     * @param Request $request
     * @param WorkingHoursRepository $rep
     * @return JsonResponse
     * @throws \Exception
     */
    public function test_admin(User $user, Request $request, WorkingHoursRepository $rep): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            if($request->request->get('start')===null && $request->request->get('end')===null){
                return new JsonResponse([
                    'success'  => false,
                ]);
            }else{
                $workingHours = $rep->findOneBy(["business"=>$user]);
                $test = $workingHours->getHours();
                //dump($test);
                $mergedRanges = OpeningHours::mergeOverlappingRanges($test);
                $openingHours= OpeningHours::create($mergedRanges);
                // $day =$request->request->get('day');
                $start = $request->request->get('start');
                $end = $request->request->get('end');
                dump($start);
                dump($end);
                // die;
                $res = $openingHours->diffInOpenMinutes(new DateTime($start), new DateTime($end));


                $checkTime = strtotime($start);
                $loginTime = strtotime($end);
                $diff =  ($loginTime - $checkTime)/60 ;
                dump($diff);
                dump($res);
                dump($diff<=$res);
                //dump($day);
                return new JsonResponse([
                    'success'  => true,
                    'result' => $diff<=$res
                ]);
            }
        }
        return new JsonResponse([
            'success'  => false,
        ]);
    }

}
