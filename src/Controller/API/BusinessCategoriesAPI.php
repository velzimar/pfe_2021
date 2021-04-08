<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/businessCategories")
 * @method User|null getUser()
 */
class BusinessCategoriesAPI extends AbstractFOSRestController
{

    private $categoryRepository;

    //  private $manager;

    function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        // $this->manager = $this->getDoctrine()->getManager();
    }


    /**
     * @Rest\Get(name="businessCategories_list", "/")
     * @return Response
     */
    public function getbusinessCategories_listAction(): Response
    {
        $products = $this->categoryRepository->findAllAPI();
        $view = $this->view([
            'success' => true,
            'businessCategories' => $products
        ]);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get(name="businessCategories_notEmpty_list", "/notEmpty")
     * @return Response
     */
    public function getbusinessCategories_notEmpty_listAction(): Response
    {

        $products = $this->categoryRepository->findAllAPINotEmpty();
        $view = $this->view([
            'success' => true,
            'businessCategories' => $products
        ]);
        return $this->handleView($view);
    }

    
    /**
     * @Rest\Get(name="businessCategories_notEmpty_byName_list", "/notEmptyByName/")
     * @param ParamFetcher $paramFetcher
     * @QueryParam(name="searchParam", nullable=false)
     * @return Response
     */
    public function getbusinessCategories_notEmpty_byName_listAction(ParamFetcher $paramFetcher): Response
    {

        $searchParam = $paramFetcher->get('searchParam');
        if ($searchParam != null && $searchParam != "") {

            $products = $this->categoryRepository->findByBusinessNameAPINotEmpty($searchParam);
            $view = $this->view([
                'success' => true,
                'businessCategories' => $products
            ]);
            return $this->handleView($view);
        } else {
            $products = $this->categoryRepository->findAllAPINotEmpty();
            $view = $this->view([
                'success' => true,
                'businessCategories' => $products
            ]);
            return $this->handleView($view);
        }
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
