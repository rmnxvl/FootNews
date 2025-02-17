<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Repository\UserRepository;
use App\Repository\ResetPasswordRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ResetPasswordController extends AbstractController
{
    #[Route('/password_reset', name: 'password_reset')]
    public function requestReset(Request $request, UserRepository $userRepo, EntityManagerInterface $em, MailService $mailService): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepo->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('danger', 'Email inconnu.');
                return $this->redirectToRoute('password_reset');
            }

            $token = Uuid::v4();
            $reset = new ResetPassword();
            $reset->setEmail($email);
            $reset->setToken($token);
            $reset->setExpiresAt(new \DateTime('+1 hour'));

            $em->persist($reset);
            $em->flush();

            $mailService->sendEmail(
                $email,
                'Réinitialisation de votre mot de passe',
                "<p>Cliquez ici pour réinitialiser votre mot de passe :</p>
                 <a href='http://localhost:8000/reset-password/$token'>Réinitialiser</a>"
            );

            $this->addFlash('success', 'Un e-mail a été envoyé.');
            return $this->redirectToRoute('password_reset');
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'password_reset_confirm')]
    public function resetPassword($token, ResetPasswordRepository $resetRepo, EntityManagerInterface $em, Request $request, UserRepository $userRepo): Response
    {
        $reset = $resetRepo->findOneBy(['token' => $token]);

        if (!$reset || $reset->getExpiresAt() < new \DateTime()) {
            $this->addFlash('danger', 'Lien invalide ou expiré.');
            return $this->redirectToRoute('password_reset');
        }

        if ($request->isMethod('POST')) {
            $newPassword = password_hash($request->request->get('password'), PASSWORD_DEFAULT);
            $user = $userRepo->findOneBy(['email' => $reset->getEmail()]);
            $user->setPassword($newPassword);

            $em->persist($user);
            $em->remove($reset);
            $em->flush();

            $this->addFlash('success', 'Mot de passe mis à jour.');
            return $this->redirectToRoute('app_login');
        }

    return $this->render('reset_password/reset.html.twig', [
        'token' => $token
    ]);
}
}