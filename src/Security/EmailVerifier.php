<?php

namespace App\Security;

use App\Models\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier {
	public function __construct(private readonly VerifyEmailHelperInterface $verifyEmailHelper, private readonly MailerInterface $mailer, private readonly EntityManagerInterface $entityManager) {
	}
	
	/**
	 * @throws TransportExceptionInterface
	 */
	public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void {
		$signatureComponents = $this->verifyEmailHelper->generateSignature($verifyEmailRouteName, (string)$user->getId(), (string)$user->getEmail());
		
		$context = $email->getContext();
		$context["signedUrl"] = $signatureComponents->getSignedUrl();
		$context["expiresAtMessageKey"] = $signatureComponents->getExpirationMessageKey();
		$context["expiresAtMessageData"] = $signatureComponents->getExpirationMessageData();
		
		$email->context($context);
		$this->mailer->send($email);
	}
	
	public function handleEmailConfirmation(Request $request, User $user): void {
		$this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string)$user->getId(), (string)$user->getEmail());
		
		$user->setIsVerified(true);
		
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}
}
