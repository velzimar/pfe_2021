<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Form\API\UserPersonalInfoAPIType;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Request\ParamFetcher;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @throws \Exception
     * @QueryParam(name="user", default="", strict=true)
     */
    public function patchpersonalInfoAction(ParamFetcher $paramFetcher, Request $request): Response
    {
        $json= $request->getContent();
        if ($decodedJson = json_decode($json, true)) {
            $data = $decodedJson;
        } else {
            $data = $request->request->all();
        }
        if (!isset($data["nom"]) || !isset($data["prenom"]) || !isset($data["cin"]) || !isset($data["phone"])) {
            $view = $this->view(["success" => false]);
            return $this->handleView($view);
        }
        $nom = $request->get('nom');
        $prenom = $request->get('prenom');
        $cin = $request->get('cin');
        $phone = $request->get('phone');
        $id = $paramFetcher->get('user');
        $user = $this->userRepository->find(["id"=>$id]);
        $form = $this->createForm(UserPersonalInfoAPIType::class, $user);
       // $form->handleRequest($request);
/*
        $view = $this->view(["success" => $request]);
        return $this->handleView($view);
*/
        if ($request->isMethod('PATCH')) {

/*
            $view = $this->view([
                "message" => $request
            ]);
            return $this->handleView($view);
*/
            /*
            $formData = [];
            foreach ($form->all() as $name => $field) {
                if (isset($data[$name])) {
                    $formData[$name] = $data[$name];
                }
            }
*/
            $form->submit($data);

/*
            $view = $this->view([
              //  "messaerrge" => $form->isValid(),
                "message" => $form->get('prenom')->getData(),
                "messagee" => $form->get('cin')->getData(),
                "messagse" => $form->get('phone')->getData(),
                "messagqse" => $form->get('nom')->getData(),
            ]);
            return $this->handleView($view);
*/
            if ($form->isSubmitted() && $form->isValid()) {
                // perform some action...
                $entityManager = $this->getDoctrine()->getManager();
                $user->setCin($cin);
                $user->setPhone($phone);
                $user->setPrenom($prenom);
                $user->setNom($nom);
                $entityManager->flush();
                $view = $this->view([
                    "message" => "Info updated"
                ]);
                return $this->handleView($view);
            }
        }

        $view = $this->view([
            "message" => "none"
        ]);
        return $this->handleView($view);

    }



}