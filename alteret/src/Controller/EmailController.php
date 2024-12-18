<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmailController extends AbstractController
{
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('bensouidenemedyassine@gmail.com') // Votre adresse Gmail
            ->to('bensouidenemedyassine@gmail.com')          // Adresse du destinataire
            ->subject('Test Email avec Gmail')
            ->text('Ceci est un email de test envoyé via Gmail et Symfony.')
            ->html('<p>Ceci est un email de test envoyé via <b>Gmail</b> et <b>Symfony</b>.</p>');

        try {
            $mailer->send($email);
            return new Response('Email envoyé avec succès via Gmail !');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
}