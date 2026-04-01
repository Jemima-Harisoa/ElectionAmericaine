<?php 

namespace app\services;

use app\repositories\UserRepository;

/**
 * Service d'authentification pour gérer les connexions, déconnexions et l'état de l'utilisateur
 */
class AuthService
{
	private UserRepository $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * Vérifie les identifiants puis crée la session.
	 */
	public function login(string $username, string $password): bool
	{
		$username = trim($username);
		if ($username === '' || $password === '') {
			return false;
		}

		$user = $this->userRepository->getByUsername($username);
		if ($user === false) {
			return false;
		}

		if ($this->userRepository->verifyPassword($password, (string) $user['password_hash']) === false) {
			return false;
		}

		$this->startSessionIfNeeded();
		session_regenerate_id(true);

		$_SESSION['user'] = [
			'id' => (int) $user['id'],
			'username' => (string) $user['username'],
			'role' => (string) $user['role'],
		];

		return true;
	}

	/**
	 * Détruit la session courante.
	 */
	public function logout(): void
	{
		$this->startSessionIfNeeded();

		$_SESSION = [];

		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}

		session_destroy();
	}

	/**
	 * Retourne les infos de l'utilisateur connecté, sinon null.
	 *
	 * @return array{id:int, username:string, role:string}|null
	 */
	public function getCurrentUser(): ?array
	{
		$this->startSessionIfNeeded();

		if (isset($_SESSION['user']) === false || is_array($_SESSION['user']) === false) {
			return null;
		}

		return $_SESSION['user'];
	}

	/**
	 * Vérifie si l'utilisateur connecté est admin.
	 */
	public function isAdmin(): bool
	{
		$user = $this->getCurrentUser();

		return $user !== null && (($user['role'] ?? null) === 'admin');
	}

	/**
	 * Vérifie qu'une session utilisateur est active.
	 */
	public function requireAuth(): bool
	{
		return $this->getCurrentUser() !== null;
	}

	/**
	 * Vérifie que l'utilisateur connecté est admin.
	 */
	public function requireAdmin(): bool
	{
		return $this->getCurrentUser() !== null && $this->isAdmin();
	}

	private function startSessionIfNeeded(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
	}
}