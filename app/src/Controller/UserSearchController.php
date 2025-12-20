<?php
// app/src/Controller/UserSearchController.php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;

final class UserSearchController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}
    
    #[Route('/user/search', name: 'app_user_search')]
    public function index(Request $request): Response
    {
        $q = (string) $request->query->get('q', '');
        $limit = (int) $request->query->get('limit', 50);
        $limit = max(1, min(100, $limit));
        
        if($q !== '') {
            $users = $this->userRepository->searchByUsernameLike($q, $limit);
        } else {
            $users = [];
        }
        return $this->render('user_search/index.html.twig', [
            'controller_name' => 'UserSearchController',
            'users' => $users,
        ]);
    }

    #[Route('/user/search-dbal', name: 'app_user_search_dbal')]
    public function dbal(Request $request, Connection $conn): Response
    {
        $q = (string) $request->query->get('q', '');
        $limit = (int) $request->query->get('limit', 50);
        $limit = max(1, min(100, $limit));

        $rows = [];
        if ($q !== '') {
            // LIMIT cannot be bound reliably in all DBs; inject safe int
            $sql = "SELECT id, username, roles FROM user WHERE username LIKE :q ORDER BY id ASC LIMIT $limit";
            $rows = $conn->executeQuery($sql, ['q' => '%' . $q . '%'])->fetchAllAssociative();
        }
        
        return $this->render('user_search/index.html.twig', [
            'controller_name' => 'UserSearchController',
            'users' => $rows,
        ]);
    }
}
