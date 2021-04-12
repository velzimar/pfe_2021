<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use App\Entity\WorkingHours;
use App\Form\ServiceType;
use App\Form\SelectUserType;
use App\Form\WorkingHoursType;
use App\Repository\ServiceRepository;
use App\Repository\WorkingHoursRepository;
use DateTime;
use Spatie\OpeningHours\OpeningHours;

/**
 * @Route("/api/businesses")
 * @method User|null getUser()
 */
class BusinessesAPI extends AbstractFOSRestController
{

    private $userRepository;

    //  private $manager;

    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        // $this->manager = $this->getDoctrine()->getManager();
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

    //near means in a 1 km range
    function isNear(float $latitude1,float  $longitude1,float  $latitude2,float  $longitude2) {
        return($this->getDist($latitude1, $longitude1, $latitude2, $longitude2)<=1);
    }

    /**
     * @Rest\POST(name="Top4businessesByCategory", "/getTop4/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @QueryParam(name="name", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function postTop4businessesByCategory_listAction(ParamFetcher $paramFetcher): Response
    {       
        
        $id = $paramFetcher->get('id');
        $name = $paramFetcher->get('name');
        $businesses = $this->userRepository->findTop4ForEachCategory($id,$name);
        $view = $this->view([
            'success' => true,
            'id' => $id,
            'searchParam' => $name,
            'count'=>sizeof($businesses),
            'businesses' => $businesses
        ]);
        return $this->handleView($view);
    }


    // show open and closed shops



    /**
     * @Rest\GET(name="businessesOfACategory", "/ofCategory/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getbusinessesOfACategory_listAction(ParamFetcher $paramFetcher,WorkingHoursRepository $workingHoursRepository): Response
    {       
        //category id
        $id = $paramFetcher->get('id');
        $businesses = $this->userRepository->findBusinessesOfACategory($id);

        //adding status of that shop 
        $newBusinesses = [];
        foreach($businesses as $business){
            $businessId = $business["id"];
            $haveOpeningHours = $workingHoursRepository->findOneBy(["business"=> $businessId]);
            if($haveOpeningHours!=null){
                $ranges = $haveOpeningHours->getHours();
                $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
                array_push($newBusinesses,$business+=['isOpen'=>$openingHours->isOpen()]);
            }else{
                array_push($newBusinesses,$business+=['isOpen'=>false]);
            }
        }
        
        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses,
            'newBusinesses' => $newBusinesses,
            //'test' => $test,

        ]);
        return $this->handleView($view);
    }
      /**
     * @Rest\GET(name="businessesOfACategory_ByName", "/ofCategory/ByName/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @QueryParam(name="name", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getbusinessesOfACategoryByName_listAction(ParamFetcher $paramFetcher,WorkingHoursRepository $workingHoursRepository): Response
    {       
        //category id
        $id = $paramFetcher->get('id');
        //nom de business
        $name = $paramFetcher->get('name');
        $businesses = $this->userRepository->findBusinessesOfACategoryByName($id,$name);

        $newBusinesses = [];
        foreach($businesses as $business){
            $businessId = $business["id"];
            $haveOpeningHours = $workingHoursRepository->findOneBy(["business"=> $businessId]);
            if($haveOpeningHours!=null){
                $ranges = $haveOpeningHours->getHours();
                $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
                array_push($newBusinesses,$business+=['isOpen'=>$openingHours->isOpen(),'nextOpen'=>'Ouvre le '.$openingHours->nextOpen(new DateTime('now'))->format('d/m H:i')]);
            }else{
                array_push($newBusinesses,$business+=['isOpen'=>false,'nextOpen'=>'Ouvre le '.$openingHours->nextOpen(new DateTime('now'))->format('d/m H:i')]);
            }
        }


        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses,
            'newBusinesses' => $newBusinesses
        ]);
        return $this->handleView($view);
    }
      /**
     * @Rest\GET(name="businessesOfACategoryWithDelivery_ByName", "/ofCategory/ByName/WithDelivery/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @QueryParam(name="name", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getbusinessesOfACategoryByNameWithDelivery_listAction(ParamFetcher $paramFetcher,WorkingHoursRepository $workingHoursRepository): Response
    {       
        //category id
        $id = $paramFetcher->get('id');
        //nom de business
        $name = $paramFetcher->get('name');
        $businesses = $this->userRepository->findBusinessesOfACategoryByNameWithDelivery($id,$name);
        $newBusinesses = [];
        foreach($businesses as $business){
            $businessId = $business["id"];
            $haveOpeningHours = $workingHoursRepository->findOneBy(["business"=> $businessId]);
            if($haveOpeningHours!=null){
                $ranges = $haveOpeningHours->getHours();
                $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
                array_push($newBusinesses,$business+=['isOpen'=>$openingHours->isOpen()]);
            }else{
                array_push($newBusinesses,$business+=['isOpen'=>false]);
            }
        }
        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses,

            'newBusinesses' => $newBusinesses,
        ]);
        return $this->handleView($view);
    }

    // show open shops only



    /**
     * @Rest\GET(name="businessesOfACategoryOpen", "/ofCategory/Open/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */

    public function getbusinessesOfACategoryOpen_listAction(ParamFetcher $paramFetcher,WorkingHoursRepository $workingHoursRepository): Response
    {
        //category id
        $id = $paramFetcher->get('id');
        $businesses = $this->userRepository->findBusinessesOfACategory($id);

        //adding status of that shop
        $newBusinesses = [];
        foreach($businesses as $business){
            $businessId = $business["id"];
            $haveOpeningHours = $workingHoursRepository->findOneBy(["business"=> $businessId]);
            if($haveOpeningHours!=null){
                $ranges = $haveOpeningHours->getHours();
                $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
                if($openingHours->isOpen())
                    array_push($newBusinesses,$business+=['isOpen'=>$openingHours->isOpen()]);
            }
        }

        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses,
            'newBusinesses' => $newBusinesses,
            //'test' => $test,

        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\GET(name="businessesOfACategory_openOnly_ByName", "/ofCategory/ByName/Open/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @QueryParam(name="name", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */

    public function getbusinessesOfACategoryByName_openOnly_listAction(ParamFetcher $paramFetcher,WorkingHoursRepository $workingHoursRepository): Response
    {
        //category id
        $id = $paramFetcher->get('id');
        //nom de business
        $name = $paramFetcher->get('name');
        $businesses = $this->userRepository->findBusinessesOfACategoryByName($id,$name);

        $newBusinesses = [];
        foreach($businesses as $business){
            $businessId = $business["id"];
            $haveOpeningHours = $workingHoursRepository->findOneBy(["business"=> $businessId]);
            if($haveOpeningHours!=null){
                $ranges = $haveOpeningHours->getHours();
                $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');

                if($openingHours->isOpen())
                array_push($newBusinesses,$business+=['isOpen'=>$openingHours->isOpen()]);
            }
        }


        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses,
            'newBusinesses' => $newBusinesses
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\GET(name="businessesOfACategoryWithDelivery_openOnly_ByName", "/ofCategory/ByName/WithDelivery/Open/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @QueryParam(name="name", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */

    public function getbusinessesOfACategoryByNameWithDelivery_openOnly_listAction(ParamFetcher $paramFetcher,WorkingHoursRepository $workingHoursRepository): Response
    {
        //category id
        $id = $paramFetcher->get('id');
        //nom de business
        $name = $paramFetcher->get('name');
        $businesses = $this->userRepository->findBusinessesOfACategoryByNameWithDelivery($id,$name);
        $newBusinesses = [];
        foreach($businesses as $business){
            $businessId = $business["id"];
            $haveOpeningHours = $workingHoursRepository->findOneBy(["business"=> $businessId]);
            if($haveOpeningHours!=null){
                $ranges = $haveOpeningHours->getHours();
                $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');

                if($openingHours->isOpen())
                array_push($newBusinesses,$business+=['isOpen'=>$openingHours->isOpen()]);
            }
        }
        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses,

            'newBusinesses' => $newBusinesses,
        ]);
        return $this->handleView($view);
    }


    /**
     * @Rest\GET(name="businessesOfACategoryWithDelivery_openOnly_nearby_ByName", "/ofCategory/ByName/WithDelivery/Open/Nearby/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @QueryParam(name="name", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */

    public function getbusinessesOfACategoryByNameWithDelivery_openOnly_nearby_listAction(ParamFetcher $paramFetcher,WorkingHoursRepository $workingHoursRepository): Response
    {
        //category id
        $id = $paramFetcher->get('id');
        //nom de business
        $name = $paramFetcher->get('name');
        $businesses = $this->userRepository->findBusinessesOfACategoryByNameWithDelivery($id,$name);
        $newBusinesses = [];
        foreach($businesses as $business){
            $businessId = $business["id"];
            $haveOpeningHours = $workingHoursRepository->findOneBy(["business"=> $businessId]);
            if($haveOpeningHours!=null){
                $ranges = $haveOpeningHours->getHours();
                $openingHours=OpeningHours::createAndMergeOverlappingRanges($ranges, '+01:00');
                $myLat = 10.264;
                $myLng = 36.75428977;
                if($openingHours->isOpen() && $this->isNear(floatval($business["longitude"]),floatval($business["latitude"]),$myLat,$myLng))
                    array_push($newBusinesses,$business+=['isOpen'=>$openingHours->isOpen(),'distanceInKm'=>$this->getDist(floatval($business["longitude"]),floatval($business["latitude"]),$myLat,$myLng)]);
            }
        }
        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses,

            'newBusinesses' => $newBusinesses,
        ]);
        return $this->handleView($view);
    }




}
