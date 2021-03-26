<?php

namespace App\Controller\API;

use App\Form\API\UserImageAPIType;
use App\Form\API\UserPersonalInfoAPIType;
use App\Repository\UserRepository;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Request\ParamFetcher;
use InvalidArgumentException;
use Swift_Mailer;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/account")
 */
class AccountAPI extends AbstractFOSRestController
{

    private $userRepository;
    private $mailer;

    public function __construct(Swift_Mailer $mailer, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    //anonymous

    /**
     * @Rest\Patch ("/personalInfo/", name="api_edit_personal_info")
     * @param ParamFetcher $paramFetcher
     * @param Request $request
     * @return Response
     * @throws Exception
     * @QueryParam(name="user", default="", strict=true)
     */
    public function patchpersonalInfoAction(ParamFetcher $paramFetcher, Request $request): Response
    {
        $json = $request->getContent();
        //getting json body
        if ($decodedJson = json_decode($json, true)) {
            $data = $decodedJson;
        } else {
            $data = $request->request->all();
        }
        //key verification
        if (!isset($data["nom"]) || !isset($data["prenom"]) || !isset($data["cin"]) || !isset($data["phone"])) {
            $view = $this->view([
                "message" => "Check body keys",
                "success" => false
            ]);
            return $this->handleView($view);
        }
        $nom = $request->get('nom');
        $prenom = $request->get('prenom');
        $cin = $request->get('cin');
        $phone = $request->get('phone');
        $id = $paramFetcher->get('user');
        $user = $this->userRepository->find(["id" => $id]);

        $form = $this->createForm(UserPersonalInfoAPIType::class, $user);
        if ($request->isMethod('PATCH')) {
            try {
                $form->submit($data);
            } catch (InvalidArgumentException $e) {
                $view = $this->view([
                    "success" => false,
                    "message" => "Valider les données"
                ]);
                return $this->handleView($view);
            }
            //validate values
            if ($form->isSubmitted() && $form->isValid()) {
                $user->setCin($cin);
                $user->setPhone($phone);
                $user->setPrenom($prenom);
                $user->setNom($nom);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                $view = $this->view([
                    "success" => true,
                    "message" => "Info updated"
                ]);
                return $this->handleView($view);
            }
        }
        $view = $this->view([
            "success" => false,
            "message" => "Check request body"
        ]);
        return $this->handleView($view);
    }


}