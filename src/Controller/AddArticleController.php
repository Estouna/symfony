<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddArticleController extends AbstractController
{
    #[Route('ajoutArticle', name: 'app_add_article')]
    public function index(): Response
    {
        return $this->render('ajoutArticle/index.html.twig', [
            'controller_name' => 'AddArticleController',
        ]);
    }
}
