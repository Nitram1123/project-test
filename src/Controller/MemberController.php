<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    #[Route('/api/login_check', name: 'app_security_login', methods:['POST'])]
    public function login(): void
    {
    }

    #[Route('/api/members/roles', name: 'app_security_roles', methods:['GET'])]
    public function getMemberRoles(): JsonResponse
    {
        return $this->json([$this->getUser()->getRoles()]);
    }
}
