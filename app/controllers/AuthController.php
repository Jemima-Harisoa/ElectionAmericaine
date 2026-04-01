<?php

namespace app\controllers;

use app\services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin(): void
    {
        if ($this->authService->requireAuth()) {
            \Flight::redirect('/saisie');
            return;
        }

        \Flight::render('auth/login', [
            'error' => null,
            'username' => '',
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Connexion',
            'showNavbar' => false,
            'currentUser' => null,
        ]);
    }

    public function handleLogin(): void
    {
        $request = \Flight::request();
        $username = trim((string) ($request->data->username ?? ''));
        $password = (string) ($request->data->password ?? '');

        if ($this->authService->login($username, $password)) {
            \Flight::redirect('/saisie');
            return;
        }

        \Flight::render('auth/login', [
            'error' => 'Nom d\'utilisateur ou mot de passe invalide.',
            'username' => $username,
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Connexion',
            'showNavbar' => false,
            'currentUser' => null,
        ]);
    }

    public function handleLogout(): void
    {
        $this->authService->logout();
        \Flight::redirect('/login');
    }
}
