<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Articles;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticlesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\ArticlesRepository;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'Accueil administration',
        ]);
    }

    #[Route('/articles', name: 'addArticle')]
    // Pour gérer un formulaire il est nécessaire d'avoir en paramètre Request qui permet de recevoir les données de notre formulaire
    public function addArticle(Request $request, ArticlesRepository $articlesRepo): Response
    {
        // Crée un nouvel article
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
            $articlesRepo->add($article,true);

            return $this->redirectToRoute('admin_home');
        }
         
        return $this->render('admin/articles/addArticle.html.twig', [
            'controller_name' => 'Publier un article',
            'form' => $form->createView(),
        ]);
    }
}
