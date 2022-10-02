<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\CommentsRepository;
use App\Form\EditProfileType;


#[Route('/utilisateur', name: 'users_')]
#[IsGranted('ROLE_USER')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CommentsRepository $commentsRepo): Response
    {
        $comments = $commentsRepo->findBy(['auteur' => $this->getUser()]);

        return $this->render('users/index.html.twig', [
            'controller_name' => 'Bienvenue',
            'comments' => $comments,
        ]);
    }

    #[Route('/editer-profil', name: 'editProfil')]
    public function editProfil(): response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        return $this->render('users/editProfil.html.twig', [
            'controller_name' => 'Modifier mes informations',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/editer-commentaires', name: 'editComments')]
    public function editComments(): response
    {
        return $this->render('users/editComments.html.twig', [
            'controller_name' => 'Modifier mes commentaires',
        ]);
    }
}
