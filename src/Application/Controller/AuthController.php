<?php

namespace App\Application\Controller;

use App\Application\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /** @Route("register", methods={"POST"}, name="application_register") */
    public function register(Request $request, UserManager $userManager)
    {
        $user = $userManager->create($request->request->get('username'), $request->request->get('password'));

        if (!$user) {
            return new JsonResponse([
                'message' => 'User cannot be created with those username and password'
            ], 401);
        }

        $userManager->save($user);

        return new JsonResponse([
            'message' => sprintf('User %s successfully created', $user->getUsername())
        ]);
    }
}