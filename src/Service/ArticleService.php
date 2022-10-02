<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ArticlesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;


class ArticleService
{
    /* 
        On récupère RequestStack, l'ArticlesRepository et PaginatorInterface qui vient du bundle KNP PAGINATOR 
        RequestStack = objet permettant de récupèrer les informations sur la requête.
    */
    public function __construct(
        private RequestStack $requestStack,
        private ArticlesRepository $articlesRepo,
        private PaginatorInterface $paginator
    ) {

    }

    /*
        La fonction récupère la requête avec RequestStack qui contient la MainRequest
        On créé une variable $page qui est égal à $request de query (que l'on récupère dans la variable $_GET) et on récupère un entier 'page', si page n'existe pas dans l'url on met par défaut 1
        On crée une limite (provisoirement 5 en dure)
        Ensuite on récupère la méthode que l'on a crée dans ArticlesRepository
        Et on fait un return de paginator avec une fonction magique paginate() qui prend plusieurs paramètres ($target = ce qui doit être paginé c'est-à-dire notre requête donc nos articles / $page = la page courante/ $limit = la limite du nombre d'articles par page / et enfin des options)
        Maintenant on peut aller modifier la page d'accueil d'articles d'abord son Controller puis dans sa vue
    */
    public function getPaginatedArticles(): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = 4;

        $articlesQuery = $this->articlesRepo->findForPagination();

        return $this->paginator->paginate($articlesQuery, $page, $limit);

    }
}