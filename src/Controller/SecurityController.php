<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // 🚀 Redirection si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home'); // Remplace 'app_home' par ta route d'accueil
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(Request $request): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Connexion API réussie',
            'token' => 'fake-jwt-token-for-demo',
        ]);
    }

    #[Route('/register', name: 'user_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $error = null;

        if ($request->isMethod('POST')) {
            $username = trim($request->request->get('username'));
            $email = trim($request->request->get('email'));
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $agreeTerms = $request->request->get('agree_terms');

            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Tous les champs sont obligatoires.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Adresse e-mail invalide.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (!$agreeTerms) {
                $error = 'Vous devez accepter les conditions d\'utilisation.';
            } else {
                $existingUsername = $em->getRepository(User::class)->findOneBy(['username' => $username]);
                $existingEmail = $em->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($existingUsername) {
                    $error = 'Ce nom d\'utilisateur est déjà utilisé.';
                } elseif ($existingEmail) {
                    $error = 'Cet email est déjà utilisé.';
                } else {
                    $user = new User();
                    $user->setUsername($username);
                    $user->setEmail($email);
                    $hashedPassword = $passwordHasher->hashPassword($user, $password);
                    $user->setPassword($hashedPassword);
                    $user->setRoles(['ROLE_USER']);

                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', 'Votre compte a été créé avec succès !');

                    return $this->redirectToRoute('app_login');
                }
            }
        }

        return $this->render('security/register.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/access-denied', name: 'app_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }
}