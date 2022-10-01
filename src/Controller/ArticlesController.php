<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticlesRepository;
use App\Repository\CommentsRepository;
use App\Entity\Comments;
use App\Form\CommentsType;
use Symfony\Component\HttpFoundation\Request;

#[Route('/articles', name: 'articles_')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ArticlesRepository $ArticlesRepo): Response
    {   
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'Articles',
            'articles' => $ArticlesRepo->findBy([],['id' => 'DESC']),
        ]);
    }

    #[Route('/lire/{id}', name: 'read', methods: ['GET', 'POST'])]
    public function lire($id, ArticlesRepository $ArticlesRepo, CommentsRepository $CommentsRepo, Request $request ): Response
    {   
        $article = $ArticlesRepo->find($id);
        $comments = $CommentsRepo->findBy(['article' => $article]);

        $addComment = new Comments;
        $form = $this->createForm(CommentsType::class, $addComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $addComment->setAuteur($this->getUser());
            $addComment->setArticle($article);
            $addComment->setDatePublication(new \DatetimeImmutable());
            $CommentsRepo->add($addComment,true);
        }


        return $this->render('articles/readArticle.html.twig', [
            'controller_name' => 'Article',
            'article' => $article,
            'comments' => $comments,
            'formComment' => $form->createView(),
        ]);
    }
}
