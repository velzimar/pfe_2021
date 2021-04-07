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


    /**
     * @Rest\POST(name="Top4businessesByCategory", "/getTop4/")
     * @QueryParam(name="id", strict=true, nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function postTop4businessesByCategory_listAction(ParamFetcher $paramFetcher): Response
    {       
        
        $id = $paramFetcher->get('id');
        $businesses = $this->userRepository->findTop4ForEachCategory($id);
        $view = $this->view([
            'success' => true,
            'id' => $id,
            'count'=>sizeof($businesses),
            'businesses' => $businesses
        ]);
        return $this->handleView($view);
    }
    
/*
    public function postListsAction(ParamFetcher $paramFetcher)
    {
        $title = $paramFetcher->get('title');
        if ($title) {
            $list = new TaskList();

            $preferences = new Preference();

            $preferences->setList($list);
            $list->setPreferences($preferences);

            $list->setTitle($title);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            return $this->view($list, Response::HTTP_CREATED);
        }

        return $this->view(['title' => 'This cannot be null'], Response::HTTP_BAD_REQUEST);
    }
*/


}
