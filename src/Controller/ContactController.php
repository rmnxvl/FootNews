<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Construction du formulaire
        $form = $this->createFormBuilder()
            ->add('identifiant', TextType::class, [
                'label' => 'Identifiant',
                'required' => true,
                'attr' => [
                    'placeholder' => '👤 Identifiant',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => '✉️ Email',
                ],
            ])
            ->add('sujet', ChoiceType::class, [
                'label' => 'Sujet',
                'choices' => [
                    'Support' => 'support',
                    'Articles' => 'articles',
                    'Connexion/Inscription' => 'connexion',
                    'Autres' => 'autres',
                ],
                'placeholder' => 'Choisir un sujet',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre message ici...',
                    'rows' => 6,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
            ->getForm();

        // Gère la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Exemple d'envoi d'email
            $email = (new Email())
                ->from($data['email'])
                ->to('romain.valenchon@gmail.com') // Change ceci par ton email
                ->subject($data['sujet'])
                ->text(sprintf(
                    "Identifiant : %s\nEmail : %s\nMessage :\n%s",
                    $data['identifiant'],
                    $data['email'],
                    $data['message']
                ));

            $mailer->send($email);

            // Ajout d'un message de succès
            $this->addFlash('success', 'Votre message a été envoyé avec succès.');

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}