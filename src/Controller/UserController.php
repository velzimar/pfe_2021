<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\ProductCategory;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\MyPassword_change;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 * @method User|null getUser()
 */

class UserController extends AbstractController
{
    //SUPERADMIN
    /**
     * @Route("/super/", name="super_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index_admins(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/super/new", name="super_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function new_super(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            if ($form->get('admin')->getData() == true) {

                $user->addRole('ROLE_ADMIN');
            } else {

                $user->removeRoles('ROLE_ADMIN');
            }
            if ($form->get('vendeur')->getData() == true) {
                $user->addRole('ROLE_SELLER');
            } else {
                $user->removeRoles('ROLE_SELLER');
            }

            $this->addFlash('success', 'Utilisateur ajouté avec succès');
            $entityManager->persist($user);
            if ($form->get('vendeur')->getData() == true) {
                $defaultCategory = new ProductCategory();
                $defaultCategory->setBusinessId($user);
                $defaultCategory->setNom("Ma première catégorie");
                $defaultCategory->setDescription("Catégorie par défaut.");
                $entityManager->persist($defaultCategory);
            }

            $entityManager->flush();
            if ($form->get('isActive')->getData()) {
                $notification = new Notification();
                $notification
                    ->setTitle("Votre compte est activé")
                    ->setSender($this->getUser())
                    ->setReceiver($user)
                    ->setSeen(false);
                $entityManager->persist($notification);
            }
            $entityManager->flush();
            return $this->redirectToRoute('super_index');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('verify_email_error', 'Inserer votre géolocalisation');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/super/{id}/profilePassword/edit", name="super_profile_password_edit", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param User $user
     * @return Response
     */
    public function userPasswordEditSuper(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
        $form = $this->createForm(myPassword_change::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('password')->getData();
            $new_pwd_conf = $form->get('confirm')->getData();
            /*
                        dump($this->getUser()->getPassword());
                        dump($checkPass);
                        die();
              */
            if($new_pwd_conf===$new_pwd) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $new_pwd
                    )
                );
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('user/edit.html.twig_success', 'Modification avec succès');

            }else{
                $this->addFlash('user/edit.html.twig_success', "Vérifier la confirmation du mot de passe");
            }
        }

        return $this->render("user/userPassword_edit.html.twig", [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }


    /**
     * @Route("/super/{id}/edit", name="super_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */

    public function editSuper(Request $request, User $user): Response
    {
        $lastMail = $user->getEmail();
        $isAdmin = $user->hasRole("ROLE_ADMIN");
        $isSeller = $user->hasRole("ROLE_SELLER");
        $form = $this->createForm(EditUserType::class, $user);
        $form->get('admin')->setData($isAdmin);
        $form->get('vendeur')->setData($isSeller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('admin')->getData() == true) {

                $user->addRole('ROLE_ADMIN');
            } else {

                $user->removeRoles('ROLE_ADMIN');
            }

            if ($form->get('vendeur')->getData() == true) {

                $user->addRole('ROLE_SELLER');
            } else {

                $user->removeRoles('ROLE_SELLER');
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            $this->addFlash('user/edit.html.twig_success', 'Modification avec succès');
            return $this->render('user/edit.html.twig', [
                'form' => $form->createView(),
                'user'=>$user
            ]);
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('user/edit.html.twig_error', 'Email doit être unique');
            $lastForm = $this->createForm(EditUserType::class, $user);
            $lastForm->get('email')->setData($lastMail);
            return $this->render('user/edit.html.twig', [
                'form' => $lastForm->createView(),
                'user'=>$user
            ]);
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user'=>$user
        ]);
    }



    /**
     * @Route("/super/{id}", name="super_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('super_index');
    }

    /**
     * @Route("/super/toggle/{id}", name="super_toggle_active", methods={"GET","POST"})
     * @param User $user
     * @return Response
     */
    public function toggleActiveSuper(User $user): Response
    {
        $user->setIsActive(!$user->getIsActive());
        $manager = $this->getDoctrine()->getManager();
        if ($user->getIsActive()) {
            $notification = new Notification();
            $notification
                ->setTitle("Votre compte est activé")
                ->setSender($this->getUser())
                ->setReceiver($user)
                ->setSeen(false);
            $manager->persist($notification);
        }
        $manager->flush();
        return $this->redirectToRoute('super_index');
    }

    //ADMINISTRATEUR

    /**
     * @Route("/admin/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index_users(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [//find sellers only
            'users' => $userRepository->findByRole("ROLE_SELLER"),
        ]);
    }


    /**
     * @Route("/admin/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();


            $this->addFlash('success', 'Utilisateur ajouté avec succès');
            $user->addRole("ROLE_SELLER");
            $entityManager->persist($user);

                $defaultCategory = new ProductCategory();
                $defaultCategory->setBusinessId($user);
                $defaultCategory->setNom("Ma première catégorie");
                $defaultCategory->setDescription("Catégorie par défaut.");
                $entityManager->persist($defaultCategory);

            $entityManager->flush();
            if ($form->get('isActive')->getData()) {
                $notification = new Notification();
                $notification
                    ->setTitle("Votre compte est activé")
                    ->setSender($this->getUser())
                    ->setReceiver($user)
                    ->setSeen(false);
                $entityManager->persist($notification);
            }
            $entityManager->flush();
            return $this->redirectToRoute('user_index');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('verify_email_error', 'Inserer votre géolocalisation');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }



    /**
     * @Route("/admin/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */

    public function edit(Request $request, User $user): Response
    {
        $lastMail = $user->getEmail();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            $this->addFlash('user/edit.html.twig_success', 'Modification avec succès');
            return $this->render('user/edit.html.twig', [
                'form' => $form->createView(),
                'user'=>$user
            ]);
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('user/edit.html.twig_error', 'Email doit être unique');
            $lastForm = $this->createForm(EditUserType::class, $user);
            $lastForm->get('email')->setData($lastMail);
            return $this->render('user/edit.html.twig', [
                'form' => $lastForm->createView(),
                'user'=>$user
            ]);
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user'=>$user
        ]);
    }



    /**
     * @Route("/admin/{id}/profilePassword/edit", name="user_profile_password_edit", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param User $user
     * @return Response
     */
    public function userPasswordEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
        $form = $this->createForm(myPassword_change::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('password')->getData();
            $new_pwd_conf = $form->get('confirm')->getData();
            /*
                        dump($this->getUser()->getPassword());
                        dump($checkPass);
                        die();
              */
            if($new_pwd_conf===$new_pwd) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $new_pwd
                    )
                );
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('user/edit.html.twig_success', 'Modification avec succès');

            }else{
                $this->addFlash('user/edit.html.twig_success', "Vérifier la confirmation du mot de passe");
            }
        }

        return $this->render("user/userPassword_edit.html.twig", [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }



    /**
     * @Route("/admin/toggle/{id}", name="user_toggle_active", methods={"GET","POST"})
     * @param User $user
     * @return Response
     */
    public function toggleActive(User $user): Response
    {
        $user->setIsActive(!$user->getIsActive());
        $manager = $this->getDoctrine()->getManager();
        if ($user->getIsActive()) {
            $notification = new Notification();
            $notification
                ->setTitle("Votre compte est activé")
                ->setSender($this->getUser())
                ->setReceiver($user)
                ->setSeen(false);
            $manager->persist($notification);
        }
        $manager->flush();
        return $this->redirectToRoute('user_index');
    }



    // TEAM MEMBERS
    /**
     * @param UserRepository $rep
     * @return Response
     */
    public function teamMembers(UserRepository $rep): Response
    {
        $admins = $rep->findByRole("ROLE_ADMIN");

        return $this->render(
            'teamMembers.html.twig',
            array('admins' => $admins)
        );
    }


    //Vendeur
    /**
     * @Route("/myProfile/edit", name="my_profile_edit", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function myProfileEdit(Request $request): Response
    {
        $user = $this->getUser();
        $lastMail = $user->getEmail();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            $this->addFlash('user/edit.html.twig_success', 'Modification avec succès');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('user/edit.html.twig_error', 'Votre email doit être unique');
            $lastForm = $this->createForm(EditUserType::class, $user);
            $lastForm->get('email')->setData($lastMail);
            return $this->render('user/my_edit.html.twig', [
                'form' => $lastForm->createView(),
            ]);
        }
        return $this->render('user/my_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/myProfilePassword/edit", name="my_profile_password_edit", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function myPasswordEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(myPassword_change::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
            $new_pwd = $form->get('password')->getData();
            $new_pwd_conf = $form->get('confirm')->getData();
            $checkPass = $passwordEncoder->isPasswordValid($user, $old_pwd);
            /*
                        dump($this->getUser()->getPassword());
                        dump($checkPass);
                        die();
              */
            if($checkPass === true && $new_pwd_conf===$new_pwd) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $new_pwd
                    )
                );
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('user/edit.html.twig_success', 'Modification avec succès');

            } else if($checkPass === true){
                /*
                                $x = $new_pwd_conf===$new_pwd;
                                $b = $checkPass === true;
                                $this->addFlash('user/edit.html.twig_success', "check $x");
                                $this->addFlash('user/edit.html.twig_success', "check $b");
                                $this->addFlash('user/edit.html.twig_success', "old $old_pwd");
                                $this->addFlash('user/edit.html.twig_success', "new $new_pwd");
                                $this->addFlash('user/edit.html.twig_success', "conf $new_pwd_conf");
                                */
                $this->addFlash('user/edit.html.twig_success', "Vérifier la confirmation");
            }else{

                $this->addFlash('user/edit.html.twig_success', "Vérifier l'ancien mot de passe");
            }
        }

        return $this->render("user/myPassword_edit.html.twig", [
            'form' => $form->createView(),
        ]);
    }


}
