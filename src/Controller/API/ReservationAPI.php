<?php

namespace App\Controller\API;

use App\Entity\DealCategory;
use App\Entity\OrderDeal;
use App\Entity\User;
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
     */
    public function getAvailableSlots(ParamFetcher $paramFetcher): Response
    {
        /*
        $data = json_decode($request->getContent(), true);
        if (!isset($data["nom"])) {
            $view = $this->view(["success" => false]);
            return $this->handleView($view);
        }
        $nom = $data["nom"];
        */
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
            array_push($days[$dayOfWeek],$day->format('y-m-d'));
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
                    //dump($start);
                    //dump($end);
                    // die;
                    $res = $openingHours->diffInOpenMinutes(new DateTime($start), new DateTime($end));
                    $checkTime = strtotime($start);
                    $loginTime = strtotime($end);
                    $diff =  ($loginTime - $checkTime)/60 ;
                    //dump($diff);
                    //dump($res);
                    //dump($diff<=$res);
                    //dump($day);
                    //$isInWorkingHours = $openingHours->is
                    if(true){
                        //$slot = array_merge($slot,["res"=>$diff<=$res]);
                        //array_push($thisDayAvailableSlots,$slot);
                        array_push($thisDayAvailableSlots,[
                            "start"=>$start,
                            "end"=>$end,
                            "res"=>$diff<=$res,

                            "openin"=>new DateTime($start),
                            "ende"=>new DateTime($end),
                            "r"=>$res,
                            "rd"=>$diff,
                            "ee"=>$openingHours->isOpenAt(new DateTime($start)),
                            "ese"=>$openingHours->isOpenAt(new DateTime($end)),
                            "ex"=>$openingHours->exceptions()
                        ]);
                    }
                }
                $dayInfo = array_merge(["slots"=>$thisDayAvailableSlots],["date"=>$date]);
                array_push($dataToSend,$dayInfo);
            }
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





}
