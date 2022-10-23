<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\CommentsRepository;
use App\Form\EditProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UsersRepository;
use App\Form\EditCommentsType;
use App\Form\ResetPasswordFormType;


#[Route('/utilisateur', name: 'users_')]
#[IsGranted('ROLE_USER')]
class UsersController extends AbstractController
{
    /*
        ACCUEIL
    */
    #[Route('/', name: 'index')]
    public function index(CommentsRepository $commentsRepo): Response
    {
        $comments = $commentsRepo->findBy(['auteur' => $this->getUser()], ['id' => 'DESC']);

        return $this->render('users/index.html.twig', [
            'controller_name' => 'Bienvenue',
            'comments' => $comments,
        ]);
    }

    /*
        MODIFIER PSEUDO ET/OU EMAIL
    */
    #[Route('/editer-profil', name: 'editProfil')]
    public function editProfil(Request $request, UsersRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        // Le formulaire va aller traîter la requête
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('users_index');
        }


        return $this->render('users/editProfil.html.twig', [
            'controller_name' => 'Modifier mes informations',
            'form' => $form->createView(),
        ]);
    }

    /*
        MODIFIER MOT DE PASSE
    */
    #[Route('/modifier-pass', name: 'reset_pass')]
    public function resetPass(
        Request $request,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $usersRepository->add($user, true);

            $this->addFlash('message', 'Mot de passe changé avec succès');
            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/resetPassword.html.twig', [
            'controller_name' => 'Modifier mon mot de passe',
            'passForm' => $form->createView()
        ]);
    }

    /*
        MODIFIER COMMENTAIRES
    */
    #[Route('/editer-commentaires/{id}', name: 'editComments', methods: ['GET', 'POST'])]
    public function editComments($id, CommentsRepository $CommentsRepo, Request $request): response
    {
        $editComment = $CommentsRepo->find($id);
        $form = $this->createForm(EditCommentsType::class, $editComment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $CommentsRepo->add($editComment, true);

            $this->addFlash('message', 'Commentaire mis à jour');
            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/editComments.html.twig', [
            'controller_name' => 'Modifier mes commentaires',
            'formComment' => $form->createView(),
            'editComment' => $editComment,
        ]);
    }

    /*
        SUPPRIMER COMMENTAIRES
    */
    #[Route('/supprimer-commentaire/{id}', name: 'deleteComment')]
    public function deleteComment($id, CommentsRepository $CommentsRepo): response
    {
        $deleteComment = $CommentsRepo->find($id);
        $CommentsRepo->remove($deleteComment, true);

        $this->addFlash('message', 'Commentaire supprimé');
        return $this->redirectToRoute('users_index');
    }
}
