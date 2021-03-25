<?php

namespace App\Controller;

use App\Entity\DealCategory;
use App\Entity\ProductCategory;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthAuthenticator;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function register(Swift_Mailer $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            if($form->get('confirm')->getData()==$form->get('plainPassword')->getData())
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            else{
                $this->addFlash('register', 'Vérifier le mot de passe');
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles(["ROLE_SELLER"]);
            $user->setIsActive(0);
            $user->setToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
            $entityManager->persist($user);
            $entityManager->flush();
            //init product category
            $defaultCategory = new ProductCategory();
            $defaultCategory->setBusinessId($user);
            $defaultCategory->setNom("Ma première catégorie");
            $defaultCategory->setDescription("Catégorie par défaut.");
            $entityManager->persist($defaultCategory);
            $entityManager->flush();
            //init deal category
            $defaultCategory = new DealCategory();
            $defaultCategory->setBusinessId($user);
            $defaultCategory->setNom("Ma première catégorie");
            $defaultCategory->setDescription("Catégorie par défaut.");
            $entityManager->persist($defaultCategory);
            $entityManager->flush();
            $message = (new Swift_Message("Cliquer ici pour valider votre email"))
                ->setFrom("superadmin@looper.com")
                ->setTo($user->getEmail())
                //->setReplyTo($contact->getEmail())
                ->setBody(
                    $this->render("/registration/emailMessage.html.twig",["token"=>$user->getToken(),"user"=> $user]), 'text/html'
                );
            $mailer->send($message);
            $this->addFlash('login', 'Un email est envoyé à votre adresse');
            return $this->redirectToRoute("app_login");
            /*
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
            */
        }else if ($form->isSubmitted() && !$form->isValid()){
            if($form->get('longitude')->getData()=="")
                $this->addFlash('register', 'Inserer votre localisation');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activate/{token}/{user}", name="app_verify")
     * @param String $token
     * @param User $user
     * @param UserRepository $rep
     * @return Response
     */
    public function activate(String $token, User $user, UserRepository $rep):Response
    {
        $found = $rep->find(["id"=>$user->getId()]);
        /*
        dump($user->getId());
        dump($token);
        dump($found);
        die();
        */
        if($found!==null && $found->getToken()===$token){
            $user->setIsActive(1);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("/resend/{user}", name="resend")
     * @param Swift_Mailer $mailer
     * @param User $user
     * @return Response
     */
    public function resend(Swift_Mailer $mailer, User $user):Response
    {
        $message = (new Swift_Message("Cliquer ici pour valider votre email"))
            ->setFrom("superadmin@looper.com")
            ->setTo($user->getEmail())
            //->setReplyTo($contact->getEmail())
            ->setBody(
                $this->render("/registration/emailMessage.html.twig",["token"=>$user->getToken(),"user"=> $user]), 'text/html'
            );
        $mailer->send($message);
        $this->addFlash('login', 'Un email est envoyé à votre adresse');
        return $this->redirectToRoute("app_login");
    }
}


