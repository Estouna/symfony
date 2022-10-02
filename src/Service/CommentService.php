<?php

namespace App\Service;

use App\Entity\Articles;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CommentsRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

class CommentService
{
    public function __construct(
        private RequestStack $requestStack,
        private CommentsRepository $commentsRepo,
        private PaginatorInterface $paginator
    ) {

    }
    
    public function getPaginatedComments(?Articles $articles = null): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = 4;

        $commentsQuery = $this->commentsRepo->findForPagination($articles);

        return $this->paginator->paginate($commentsQuery, $page, $limit);

    }
}