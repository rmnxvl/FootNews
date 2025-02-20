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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

        // Vérifier si l'erreur est une instance de AuthenticationException
        $errorMessage = null;
        if ($error !== null) {
            $errorMessage = 'Les identifiants rentrés sont incorrects.';
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $errorMessage, // Message d'erreur personnalisé
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
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): Response {
        $error = null;

        if ($request->isMethod('POST')) {
            $username = trim($request->request->get('username'));
            $email = trim($request->request->get('email'));
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $agreeTerms = $request->request->get('agree_terms');

            // Vérification des champs obligatoires
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                $error = 'Tous les champs sont obligatoires.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Adresse e-mail invalide.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (!$agreeTerms) {
                $error = 'Vous devez accepter les conditions d\'utilisation.';
            } else {
                // Vérifier si l'email ou le username existent déjà
                $existingUsername = $em->getRepository(User::class)->findOneBy(['username' => $username]);
                $existingEmail = $em->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($existingUsername) {
                    $error = 'Ce nom d\'utilisateur est déjà utilisé.';
                } elseif ($existingEmail) {
                    $error = 'Cet email est déjà utilisé.';
                } else {
                    // Création de l'utilisateur et validation des contraintes
                    $user = new User();
                    $user->setUsername($username);
                    $user->setEmail($email);
                    $user->setPassword($password);

                    // Validation des contraintes du mot de passe
                    $errors = $validator->validateProperty($user, 'password');
                    if (count($errors) > 0) {
                        $error = $errors[0]->getMessage(); // Récupère le premier message d'erreur
                    } else {
                        // Hashage sécurisé du mot de passe avant stockage
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