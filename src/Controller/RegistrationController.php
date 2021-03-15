<?php

namespace App\Controller;

use App\Entity\DealCategory;
use App\Entity\ProductCategory;
use App\Entity\ServiceCategory;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthAuthenticator;
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
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserAuthAuthenticator $authenticator
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthAuthenticator $authenticator): Response
    {
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
            //init service category
            $defaultCategory = new ServiceCategory();
            $defaultCategory->setBusinessId($user);
            $defaultCategory->setNom("Ma première catégorie");
            $defaultCategory->setDescription("Catégorie par défaut.");
            $entityManager->persist($defaultCategory);

            $entityManager->flush();
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }else if ($form->isSubmitted() && !$form->isValid()){
            if($form->get('longitude')->getData()=="")
                $this->addFlash('register', 'Inserer votre localisation');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
