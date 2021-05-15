<?php

namespace App\Controller\API;

use App\Entity\Category;
use App\Entity\DealCategory;
use App\Entity\OrderDeal;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\DealCategoryRepository;
use App\Repository\DeliveryRepository;
use App\Repository\OrderDealRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductOptionsRepository;
use App\Repository\DealRepository;
use App\Repository\ReservationRepository;
use App\Repository\ServiceCalendarRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Repository\WorkingHoursRepository;
use ArrayObject;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\ConnectionException;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Spatie\OpeningHours\OpeningHours;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use const Doctrine\DBAL\DBALException;

/**
 * @Route("/api/reservation")
 * @method User|null getUser()
 */
class ReservationAPI extends AbstractFOSRestController
{

    private $rr;
    private $scr;
    private $sr;
    private $whr;

    //  private $manager;

    function __construct(ReservationRepository $rr, ServiceCalendarRepository  $scr, ServiceRepository $sr, WorkingHoursRepository $whr)
    {
        $this->rr = $rr;
        $this->scr = $scr;
        $this->sr = $sr;
        $this->whr = $whr;
        // $this->manager = $this->getDoctrine()->getManager();
    }

    /**
     * @Rest\Post(name="ReservationAPI_getSlots_ofService", "/getSlotsByService/")
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @QueryParam(name="service", nullable=false)
     * @throws \Exception
     */
    public function getAvailableSlots(ParamFetcher $paramFetcher): Response
    {
        $serviceId = $paramFetcher->get('service');
        $service = $this->sr->find($serviceId);
        $tomorrowString = date('Y-m-d',strtotime("tomorrow"));
        $tomorrowDate = new DateTime($tomorrowString);
        $period = new DatePeriod(
            $tomorrowDate, // Start date of the period
            new DateInterval('P1D'), // Define the intervals as Periods of 1 Day
            90 // Apply the interval 6 times on top of the starting date
        );
        //next 90 days list
        $days = [[],[],[],[],[],[],[]];
        foreach ($period as $day)
        {
            $dayOfWeek  = $day->format('w');
            array_push($days[$dayOfWeek],$day->format('Y-m-d'));
        }
        // workingHours List
        $workingHours = $this->whr->findOneBy(["business"=>$service->getBusiness()]);
        $workingHoursList = $workingHours->getHours();
            //convert it to openingHours
            $openingHours = OpeningHours::createAndMergeOverlappingRanges($workingHoursList);
        // slots list
        $serviceCalendar = $this->scr->findOneBy(["service"=>$service]);
        $slots = $serviceCalendar->getSlots();
        $dayOfWeek = 0;
        $dataToSend = [];
        foreach($days as $day){
            $thisDaySlots = $slots[$dayOfWeek];
            $thisDayOfWeekData = [];
            foreach($day as $date){
                // we got the date yyyy-mm-dd
                // get available slots of that day
                $thisDayAvailableSlots = [];
                foreach($thisDaySlots as $slot){
                    //check if this slot is in workingHours
                    $startTime = $slot["start"];
                    $endTime = $slot["end"];
                    $start = $date." ".$startTime;
                    $end = $date." ".$endTime;
                    $res = $openingHours->diffInOpenMinutes(new DateTime($start), new DateTime($end));
                    $checkTime = strtotime($start);
                    $loginTime = strtotime($end);
                    $diff =  ($loginTime - $checkTime)/60 ;
                    if($diff<=$res){
                        //check if reserved
                        $reservations = $this->rr->findReservationAtThisDay($start,$service,false,false);
                        array_push($thisDayAvailableSlots,[
                            "start"=>$start,
                            "end"=>$end,
                            "isInWorkingHours"=>$diff<=$res,
                            "isReserved"=>sizeof($reservations)!==0
                        ]);
                    }
                }
                if(isset($thisDayAvailableSlots[0]["isInWorkingHours"])){
                    $dayInfo = array_merge(["date"=> $date],["slots"=>$thisDayAvailableSlots]);
                    array_push($thisDayOfWeekData,$dayInfo);
                }
            }
            array_push($dataToSend,[$dayOfWeek+1=>$thisDayOfWeekData]);
            $dayOfWeek++;
        }
        $view = $this->view([
            'success' => true,
            'daysList' => $days,
            'workingHoursList' => $workingHoursList,
            'slots' => $slots,
            'data' => $dataToSend,
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post(name="ReservationAPI_get4AvailableServicesByEachCategory", "/get4AvailableServicesByEachCategory/")
     * @param CategoryRepository $businessCategoryRepository
     * @return Response
     */
    public function get4AvailableServicesByEachCategoryAction(CategoryRepository $businessCategoryRepository): Response
    {
        try{
            $categories = $businessCategoryRepository->findAll();
            $availableServices = [];
            foreach($categories as $category){
                $servicesList = $this->sr->find4AvailableByCategory($category->getId());
                if(sizeof($servicesList)>0){
                    array_push($availableServices,["id"=>$category->getId(),"name"=>$category->getNom(),"list"=>$servicesList]);
                }
            }
            $view = $this->view([
                'success' => sizeof($availableServices)!==0,
                'res' => $availableServices
            ]);
            return $this->handleView($view);
        }catch (Exception $e) {
            $view = $this->view([
                'success' => false,
                'exception'=>$e
            ]);
            return $this->handleView($view);
        }
    }

    /**
     * @Rest\Post(name="ReservationAPI_getAvailableServicesByCategory", "/getAvailableServicesByCategory/")
     * @param ParamFetcher $paramFetcher
     * @param CategoryRepository $businessCategoryRepository
     * @return Response
     * @QueryParam(name="category", nullable=false)
     */
    public function getAvailableServicesByCategory(ParamFetcher $paramFetcher,CategoryRepository $businessCategoryRepository): Response
    {
        try{
            $categoryId = $paramFetcher->get("category");
            $category = $businessCategoryRepository->find($categoryId);
                $servicesList = $this->sr->findAvailableByCategory($category);
                if(sizeof($servicesList)>0){
                    $servicesList = ["category"=>$category,"list"=>$servicesList];
                }
            $view = $this->view([
                'success' => sizeof($servicesList)!==0,
                'res' => $servicesList
            ]);
            return $this->handleView($view);
        }catch (Exception $e) {
            $view = $this->view([
                'success' => false,
                'exception'=>$e
            ]);
            return $this->handleView($view);
        }
    }

    /**
     * @Rest\Post(name="ReservationAPI_getAvailableServicesByCategoryByName", "/getAvailableServicesByCategoryByName/")
     * @param ParamFetcher $paramFetcher
     * @param CategoryRepository $businessCategoryRepository
     * @return Response
     * @QueryParam(name="category", nullable=false)
     * @QueryParam(name="name", nullable=false)
     */
    public function getAvailableServicesByCategoryByName(ParamFetcher $paramFetcher,CategoryRepository $businessCategoryRepository): Response
    {
        try{
            $categoryId = $paramFetcher->get("category");
            $name = $paramFetcher->get("name");
            $category = $businessCategoryRepository->find($categoryId);
            $servicesList = $this->sr->findAvailableByCategoryByName($category,$name);
            if(sizeof($servicesList)>0){
                $servicesList = ["category"=>$category,"list"=>$servicesList];
            }
            $view = $this->view([
                'success' => sizeof($servicesList)!==0,
                'res' => $servicesList
            ]);
            return $this->handleView($view);
        }catch (Exception $e) {
            $view = $this->view([
                'success' => false,
                'exception'=>$e
            ]);
            return $this->handleView($view);
        }
    }

    /**
     * @Rest\Post(name="ReservationAPI_getAvailableServicesByCategoryByNameNearby", "/getAvailableServicesByCategoryByNameNearby/")
     * @param ParamFetcher $paramFetcher
     * @param CategoryRepository $businessCategoryRepository
     * @return Response
     * @QueryParam(name="category", nullable=false)
     * @QueryParam(name="name", nullable=false)
     * @QueryParam(name="lat", nullable=false)
     * @QueryParam(name="lng", nullable=false)
     */
    public function getAvailableServicesByCategoryByNameNearby(ParamFetcher $paramFetcher,CategoryRepository $businessCategoryRepository): Response
    {
        try{
            $categoryId = $paramFetcher->get("category");
            $name = $paramFetcher->get("name");
            $category = $businessCategoryRepository->find($categoryId);
            $myLat = floatval($paramFetcher->get("lat"));
            $myLng = floatval($paramFetcher->get("lng"));

            if($myLat == null || $myLng == null ||$categoryId == null) {
                $view = $this->view([
                    'success' => false,
                    "code"=>300
                ]);
                return $this->handleView($view);
            }
            $servicesList = $this->sr->findAvailableByCategoryByNameNearby($category,$name);
            $nearbyServices = [];
            foreach($servicesList as $service){
                $isNear = $this->isNear(floatval($service["longitude"]),floatval($service["latitude"]),$myLat,$myLng);
                $dist = $this->getDist(floatval($service["longitude"]),floatval($service["latitude"]),$myLat,$myLng);
                if($isNear){
                    $s = array_merge($service,["distance"=>$dist]);
                    array_push($nearbyServices,$s);
                }
            }
            if(sizeof($nearbyServices)>0){
                $nearbyServices = ["category"=>$category,"list"=>$nearbyServices];
            }
            $view = $this->view([
                'success' => sizeof($nearbyServices)!==0,
                'res' => $nearbyServices,
                "code"=>400
            ]);
            return $this->handleView($view);
        }catch (Exception $e) {
            $view = $this->view([
                'success' => false,
                'exception'=>$e,
                "code"=>600
            ]);
            return $this->handleView($view);
        }
    }

    /**
     * @Rest\Post(name="ReservationAPI_getMyReservationsHistory", "/getMyReservationsHistory/")
     * @param ParamFetcher $paramFetcher
     * @param UserRepository $userRepository
     * @return Response
     * @QueryParam(name="client", nullable=false)
     */
    public function getMyReservationsHistory(ParamFetcher $paramFetcher, UserRepository  $userRepository): Response
    {
        $clientId = $paramFetcher->get("client");
        $client = $userRepository->find($clientId);
        if($clientId == null ||$client == null){
            $view = $this->view([
                'success' => false,
            ]);
            return $this->handleView($view);
        }
        $reservations = $this->rr->findReservationsByClient($client->getId());
        $view = $this->view([
            'success' => true,
            "reservations"=>$reservations
        ]);
        return $this->handleView($view);
    }



    function getDist(float $latitude1,float  $longitude1,float  $latitude2,float  $longitude2) {
        $theta = $longitude1 - $longitude2;
        $distance = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        //distance in mile
        $distance = $distance * 60 * 1.1515;
        //distance in km
        $distance = $distance * 1.609344;
        return (round($distance,2));
    }
    function isNear(float $latitude1,float  $longitude1,float  $latitude2,float  $longitude2) {
        return($this->getDist($latitude1, $longitude1, $latitude2, $longitude2)<=1);
    }

}
