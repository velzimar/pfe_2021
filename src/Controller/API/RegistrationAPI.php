<?php


namespace App\Controller\API;

use App\Entity\User;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/register")
 */
class RegistrationAPI extends AbstractFOSRestController
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
     * @Rest\Post ("/", name="api_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data["email"])|| !isset($data["password"])) {
            $view = $this->view(["success" => false]);
            return $this->handleView($view);
        }
        $email = $request->get('email');
        $user = $this->userRepository->findOneBy(["email" => $email]);
        if (!is_null($user)) {
            $view = $this->view([
                "message" => "Compte existe déja"
            ]);
            return $this->handleView($view);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $password = $request->get('password');


        $user = new User();
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setEmail($email);
        $user->setRoles(["ROLE_CLIENT"]);
        $user->setCin("");
        $user->setPhone("");
        $user->setLatitude(0);
        $user->setLongitude(0);
        $user->setPrenom("");
        $user->setNom("");
        $user->setBusinessDescription("");
        $user->setBusinessName("");

        $user->setIsActive(0);
        $user->setToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        $entityManager->persist($user);
        $entityManager->flush();
        $message = (new Swift_Message("Cliquer ici pour valider votre email"))
            ->setFrom("superadmin@looper.com")
            ->setTo($user->getEmail())
            //->setReplyTo($contact->getEmail())
            ->setBody(
                $this->render("/registration/emailMessageAPI.html.twig", ["token" => $user->getToken(), "user" => $user]), 'text/html'
            );
        $this->mailer->send($message);
        $view = $this->view([
            "message" => "Un email est envoyé à votre addresse. Veuillez confirmer",
            "token" => $user->getToken()
        ]);

        return $this->handleView($view);

    }

    //anonymous

    /**
     * @Rest\POST ("/activate/", name="api_app_verify")
     * @QueryParam(name="user", default="", strict=true)
     * @QueryParam(name="token", default="", strict=true)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */

    public function ActivateAction(ParamFetcher $paramFetcher): Response
    {
        /*
        $view = $this->view([
            "message" => $paramFetcher->get('user'),

            "messagee" => $paramFetcher->get('token')
        ]);
        return $this->handleView($view);
        */

        $token = $paramFetcher->get('token');
        $user = $paramFetcher->get('user');
        if (is_null($user) || is_null($token)) {
            $view = $this->view([
                "message" => "Vérifier les paramètres"
            ]);

            return $this->handleView($view);
        }
        $found = $this->userRepository->find(["id" => $user]);

        if ($found !== null && $found->getToken() === $token) {
            $found->setIsActive(1);
            $this->getDoctrine()->getManager()->flush();
        }

        $view = $this->view([
            "message" => "Votre compte est activé maintenant"
        ]);

        return $this->handleView($view);

    }

    //anonymous
    /**
     * @Rest\Post("/resend/", name="api_resend")
     * @QueryParam(name="user", default="", strict=true)
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function resendAction(ParamFetcher $paramFetcher): Response
    {
        $user = $paramFetcher->get('user');
        $found = $this->userRepository->find(["id" => $user]);
        if (is_null($user)|| is_null($found)) {
            $view =  $this->view([
                "message" => "Vérifier les paramètres"
            ]);

            return $this->handleView($view);
        }

        if($found->getIsActive()===1){
            $view =  $this->view([
                "message" => "Compte déja activé"
            ]);
            return $this->handleView($view);
        }

        $message = (new Swift_Message("Cliquer ici pour valider votre email"))
            ->setFrom("superadmin@looper.com")
            ->setTo($found->getEmail())
            //->setReplyTo($contact->getEmail())
            ->setBody(
                $this->render("/registration/emailMessageAPI.html.twig", ["token" => $found->getToken(), "user" => $found]), 'text/html'
            );
        $this->mailer->send($message);
        $view =  $this->view([
            "message" => "Un email est envoyé à votre addresse. Veuillez confirmer"
        ]);

        return $this->handleView($view);
    }


}