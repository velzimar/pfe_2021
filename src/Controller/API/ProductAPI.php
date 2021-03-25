<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/product")
 * @method User|null getUser()
 */
class ProductAPI extends AbstractFOSRestController
{

    private $productRepository;

    //  private $manager;

    function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        // $this->manager = $this->getDoctrine()->getManager();
    }


    /**
     * @Rest\Post(name="byName", "/byName")
     * @param Request $request
     * @return Response
     */
    public function byName(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data["nom"])) {
            $view = $this->view(["success" => false]);
            return $this->handleView($view);
        }
        $nom = $data["nom"];
        $products = $this->productRepository->findByName($nom);
        $view = $this->view([
            'success' => true,
            'products' => $products
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
