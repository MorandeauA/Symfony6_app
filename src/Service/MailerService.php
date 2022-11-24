<?php

namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $replyTo;
    public function __construct(private MailerInterface $mailer, $replyTo) {
        $this->replyTo = $replyTo;
    }
    public function sendEmail($to = 'ambroise441995@gmail.com', $content = '<p>See Twig integration for better HTML integration! </p>',$title = ''): void
    {
        $email = (new Email())
            ->from('ambroise.morandeau@gmail.com')
            ->to($to)
            ->replyTo($this->replyTo)
            ->subject($title)
            ->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);
    }
}
