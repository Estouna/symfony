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
use App\Service\ArticleService;
use App\Service\CommentService;
use App\Entity\Articles;

#[Route('/articles', name: 'articles_')]
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ArticleService $ArticleService): Response
    {   
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'Articles',
            'articles' => $ArticleService->getPaginatedArticles(),
        ]);
    }
    
    #[Route('/lire/{id}', name: 'read', methods: ['GET', 'POST'])]
    public function lire(?Articles $articles, $id, ArticlesRepository $ArticlesRepo, CommentsRepository $CommentsRepo, CommentService $commentService, Request $request ): Response
    {   
        $article = $ArticlesRepo->find($id);
        
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
            'comments' => $commentService->getPaginatedComments($articles),
            'formComment' => $form->createView(),
        ]);
    }
}

// #[Route('/lire/{id}', name: 'read', methods: ['GET', 'POST'])]
// public function lire($id, ArticlesRepository $ArticlesRepo, CommentsRepository $CommentsRepo, Request $request ): Response
// {   
//     $article = $ArticlesRepo->find($id);
//   

//     $addComment = new Comments;
//     $form = $this->createForm(CommentsType::class, $addComment);
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {

//         $addComment->setAuteur($this->getUser());
//         $addComment->setArticle($article);
//         $addComment->setDatePublication(new \DatetimeImmutable());
//         $CommentsRepo->add($addComment,true);
//     }


//     return $this->render('articles/readArticle.html.twig', [
//         'controller_name' => 'Article',
//         'article' => $article,
//         'comments' => $commentService->getPaginatedComments($articles),
//         'formComment' => $form->createView(),
//     ]);
// }