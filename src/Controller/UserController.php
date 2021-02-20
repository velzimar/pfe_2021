<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
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
            if ($form->get('admin')->getData() == true) {

                $user->addRole('ROLE_ADMIN');
            } else {

                $user->removeRoles('ROLE_ADMIN');
            }

            $this->addFlash('success', 'Utilisateur ajouté avec succès');
            $entityManager->persist($user);
            $defaultCategory = new ProductCategory();
            $defaultCategory->setBusinessId($user);
            $defaultCategory->setNom("defaultCategory");
            $defaultCategory->setDescription("default category for users");
            $entityManager->persist($defaultCategory);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
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
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $lastMail=$user->getEmail();
        $form = $this->createForm(UserType::class, $user);
        $isAdmin = $user->hasRole('ROLE_ADMIN');
        //$this->addFlash('success', "has role: $isAdmin");
        $form->get('admin')->setData($isAdmin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $form->get('confirm')->getData() == $form->get('password')->getData()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            if ($form->get('admin')->getData() == true) {
                // $this->addFlash('success', 'add');
                $user->addRole('ROLE_ADMIN');
            } else {
                // $this->addFlash('success', 'remove');
                $user->removeRoles('ROLE_ADMIN');
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('user/edit.html.twig_success', 'Modification avec succès');
        } else if ($form->isSubmitted() && $form->isValid() && $form->get('confirm')->getData() !== $form->get('password')->getData()) {
            $this->addFlash('user/edit.html.twig_error', 'Vérifier le mot de passe');
        }else if($form->isSubmitted() && !$form->isValid()){
            $this->addFlash('user/edit.html.twig_error', 'Votre email doit être unique');
            $lastForm = $this->createForm(UserType::class, $user);
            $lastForm->get('email')->setData($lastMail);
            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $lastForm->createView(),
            ]);
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
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

        return $this->redirectToRoute('user_index');
    }
}
