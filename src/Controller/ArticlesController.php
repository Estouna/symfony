<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticlesRepository;
use App\Entity\Users;

class ArticlesController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(ArticlesRepository $ArticlesRepo): Response
    {   
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'Articles',
            'articles' => $ArticlesRepo->findAll(),
        ]);
    }
}
