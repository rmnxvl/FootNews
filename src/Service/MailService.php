<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from('ton-email@gmail.com') // Remplace par ton email Gmail
            ->to($to)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }
}
