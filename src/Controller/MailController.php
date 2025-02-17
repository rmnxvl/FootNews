<?php

namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MailController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('romain.valenchon@gmail.com') // Utilise ton adresse Gmail
            ->to('romain.valenchon@gmail.com') // Remplace par un vrai email de test
            ->subject('Test Symfony Mailer')
            ->html('<p>Ceci est un test d\'e-mail envoyé avec Symfony Mailer.</p>');

        $mailer->send($email);

        return new Response('E-mail envoyé avec succès !');
    }
}
