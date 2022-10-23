<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Form\EditArticleType;
use App\Repository\CommentsRepository;

#[Route('/admin/articles', name: 'admin_articles_')]
#[IsGranted('ROLE_ADMIN')]
class ArticlesCrudControler extends AbstractController
{
    /*
        ACCUEIL ARTICLES
    */
    #[Route('/gestion-articles', name: 'index')]
    public function index(ArticlesRepository $articlesRepo): Response
    {
        $articles = $articlesRepo->findBy([],['id' => 'DESC']);

        return $this->render('admin/articles/index.html.twig', [
            'controller_name' => 'Gestion des articles',
            'articles' => $articles,
        ]);
    }

    /*
        PUBLIER UN ARTICLE
    */
    #[Route('/publier', name: 'add')]
    // Pour gérer un formulaire il est nécessaire d'avoir en paramètre Request qui permet de recevoir les données de notre formulaire
    public function addArticle(Request $request, ArticlesRepository $articlesRepo): Response
    {
        $article = new Articles;
        // Crée le formulaire (createForm()) en faisant passer ArticlesType et le nouvel article en paramètre (qui sont les données à passer)
        $form = $this->createForm(ArticlesType::class, $article);
        // Récupère les données saisies dans le formulmaire
        $form->handleRequest($request);

        // Si le formulaire à été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Champs qui ne sont pas remplis par l'utilisateur
            $article->setAuteur($this->getUser());
            $article->setDatePublication(new \DatetimeImmutable());
            $articlesRepo->add($article, true);

            $this->addFlash('message', 'Article publié');
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/articles/addArticle.html.twig', [
            'controller_name' => 'Publier un article',
            'form' => $form->createView(),
        ]);
    }

    /*
        MODIFIER ARTICLES
    */
    #[Route('/editer-articles/{id}', name: 'editArticles', methods: ['GET', 'POST'])]
    public function editComments($id, ArticlesRepository $ArticlesRepo, Request $request): response
    {
        $editArticle = $ArticlesRepo->find($id);
        $form = $this->createForm(EditArticleType::class, $editArticle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ArticlesRepo->add($editArticle, true);

            $this->addFlash('message', 'Article mis à jour');
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/articles/editArticle.html.twig', [
            'controller_name' => 'Modifier les Articles',
            'formArticle' => $form->createView(),
            'editArticle' => $editArticle,
        ]);
    }

    /*
        SUPPRIMER ARTICLES
    */
    #[Route('/supprimer-article/{id}', name: 'deleteArticle')]
    public function deleteArticle($id, ArticlesRepository $ArticlesRepo, CommentsRepository $CommentsRepo): response
    {
        $article = $ArticlesRepo->find($id);
        $comments = $CommentsRepo->findBy(['article' => $article]);

        foreach ($comments as $comment) {
            $CommentsRepo->remove($comment, true);
        }

        $ArticlesRepo->remove($article, true);

        $this->addFlash('message', 'Article supprimé');
        return $this->redirectToRoute('admin_home');
    }
}
