<?php

namespace App\Security;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker extends AbstractController implements UserCheckerInterface
{
    private $mailer;
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getIsActive()) {

            //send mail if not active
            $message = (new Swift_Message("Cliquer ici pour valider votre email"))
                ->setFrom("superadmin@looper.com")
                ->setTo($user->getEmail())
                //->setReplyTo($contact->getEmail())
                ->setBody(
                    $this->render("/registration/emailMessage.html.twig",["token"=>$user->getToken(),"user"=> $user]), 'text/html'
                );

            $this->mailer->send($message);

            $this->addFlash('login', 'Un email est envoyé à votre adresse');
            //end send
            throw new CustomUserMessageAuthenticationException(
                "Votre compte n'est pas encore activé"
            );
        }
    }



    public function checkPostAuth(UserInterface $user)
    {
        $this->checkPreAuth($user);
    }
}