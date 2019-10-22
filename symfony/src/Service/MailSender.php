<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailSender
{
    private $mailer;
    private $translator;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    public function activate(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new NamedAddress('noreply@brand.com', 'Brand Name'))
            ->to($user->getEmail())
            ->subject($this->translator->trans('service.mail_sender.activate.subject'))
            ->htmlTemplate('email/activate.html.twig')
            ->context([
                'user' => $user,
            ])
        ;

        $this->mailer->send($email);
    }

    public function resetPassword(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new NamedAddress('noreply@brand.com', 'Brand Name'))
            ->to($user->getEmail())
            ->subject($this->translator->trans('service.mail_sender.reset_password.subject'))
            ->htmlTemplate('email/reset_password.html.twig')
            ->context([
                'user' => $user,
            ])
        ;

        $this->mailer->send($email);
    }
}
