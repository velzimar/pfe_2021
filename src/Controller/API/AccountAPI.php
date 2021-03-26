<?php

namespace App\Controller\API;

use App\Form\API\UserPasswordChangeAPIType;
use App\Form\API\UserPersonalInfoAPIType;
use App\Repository\UserRepository;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use InvalidArgumentException;
use Swift_Mailer;
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


    /**
     * @Rest\Patch ("/passwordChange/", name="api_edit_password")
     * @param ParamFetcher $paramFetcher
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @QueryParam(name="user", strict=true, nullable=false)
     */
    public function patchpasswordChangeAction(ParamFetcher $paramFetcher, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        try {
            $id = $paramFetcher->get('user');
        } catch (InvalidParameterException $e) {
            $view = $this->view([
                "success" => false,
                "message" => "Check user Id param"
            ]);
            return $this->handleView($view);
        }

        $user = $this->userRepository->find(["id" => $id]);
        $json = $request->getContent();
        //getting json body
        if ($decodedJson = json_decode($json, true)) {
            $data = $decodedJson;
        } else {
            $data = $request->request->all();
        }
        //key verification
        if (!isset($data["old"]) || !isset($data["password"]) || !isset($data["confirm"])) {
            $view = $this->view([
                "message" => "Check body keys",
                "success" => false
            ]);
            return $this->handleView($view);
        }
        if (!$passwordEncoder->isPasswordValid($user, $data["old"])) {
            $view = $this->view([
                "message" => "Wrong old password",
                "success" => false
            ]);
            return $this->handleView($view);
        }
        if ($data["password"] !== $data["confirm"]) {
            $view = $this->view([
                "message" => "Wrong password confirmation",
                "success" => false
            ]);
            return $this->handleView($view);
        }

        $password = $request->get('password');
        $form = $this->createForm(UserPasswordChangeAPIType::class, $user);

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
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $password
                    )
                );
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                $view = $this->view([
                    "success" => true,
                    "message" => "Password updated"
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

    /**
     * @Rest\Get(name="api_get_personal_info", "/personalInfo/")
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @QueryParam(name="user", strict=true, nullable=false)
     */
    public function getpersonalInfoAction(ParamFetcher $paramFetcher): Response
    {
        try {
            $id = $paramFetcher->get('user');
        } catch (InvalidParameterException $e) {
            $view = $this->view([
                "success" => false,
                "message" => "Check user Id param"
            ]);
            return $this->handleView($view);
        }
        $user = $this->userRepository->findOnePersonalInfoById($id);
        $view = $this->view([
            'success' => true,
            'user' => $user
        ]);
        return $this->handleView($view);
    }


    /**
     * @Rest\Get(name="api_get_geolocation", "/geolocation/")
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @QueryParam(name="user", strict=true, nullable=false)
     */
    public function getgeolocationAction(ParamFetcher $paramFetcher): Response
    {
        try {
            $id = $paramFetcher->get('user');
        } catch (InvalidParameterException $e) {
            $view = $this->view([
                "success" => false,
                "message" => "Check user Id param"
            ]);
            return $this->handleView($view);
        }
        $user = $this->userRepository->findOneGeolocationById($id);
        $view = $this->view([
            'success' => true,
            'user' => $user
        ]);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post(name="api_post_geolocation", "/geolocation/")
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @QueryParam(name="user", strict=true, nullable=false)
     */

    public function postgeolocationAction(Request $request, ParamFetcher $paramFetcher): Response
    {
        $json = $request->getContent();
        //getting json body
        if ($decodedJson = json_decode($json, true)) {
            $data = $decodedJson;
        } else {
            $data = $request->request->all();
        }
        //key verification
        if (!isset($data["longitude"]) || !isset($data["latitude"])) {
            $view = $this->view([
                "message" => "Check body keys",
                "success" => false
            ]);
            return $this->handleView($view);
        }
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $id = $paramFetcher->get('user');
        $user = $this->userRepository->find(["id" => $id]);

        if ($request->isMethod('POST')) {
            //validate values
            try {
                $user->setLatitude(floatval($latitude));
                $user->setLongitude(floatval($longitude));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
            } catch (Exception $e) {
                $view = $this->view([
                    "success" => false,
                    "message" => "Invalid data"
                ]);
                return $this->handleView($view);
            }

            $view = $this->view([
                "success" => true,
                "message" => "Info updated"
            ]);
            return $this->handleView($view);

        }
        $view = $this->view([
            "success" => false,
            "message" => "Check request body"
        ]);
        return $this->handleView($view);
    }

}