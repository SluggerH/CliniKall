<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;


class EmailService
{
    private $mailer;
    private $adminEmail;
    private $env;
    private $translator;

    public function __construct( MailerInterface $mailer,$adminEmail, $env,TranslatorInterface $translator)
     {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
        $this->env = $env;
        $this->translator = $translator;
    }

    public function send(array $data): bool {

        # Si pas de 'to', ou env = dev, on redirige sur ADMIN_EMAIL
        if ($this->env === 'dev' || !isset($data['to'])) {
            $to = $this->adminEmail;
        } else {
            $to = $data['to'];
        }

         # Traduire le sujet
        $subject = '';
        if (isset($data['subject'])) {
         $subject = $this->translator->trans($data['subject']);
        }

        $email = (new TemplatedEmail())
            ->from($this->adminEmail)
            ->to($to)
            ->replyTo($data['replyTo'] ?? $this->adminEmail)
            ->subject($subject)
            ->htmlTemplate($data['template'])
            ->context($data['context'] ?? []);

        $this->mailer->send($email);

        return true;
    }    
}
?>