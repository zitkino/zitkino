<?php

namespace App\Controllers;

use App\Forms\RegistrationFormType;
use App\Models\Entities\User;
use App\Security\EmailVerifier;
use App\Services\MetaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends BaseController {
	public function __construct(TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService, private readonly EmailVerifier $emailVerifier) {
		parent::__construct($translator, $requestStack, $metaService);
	}
	
	#[Route(path: '/login', name: 'app_login')]
	public function login(AuthenticationUtils $authenticationUtils): Response {
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();
		
		return $this->render('security/login.html.twig', [
			'last_username' => $lastUsername,
			'error' => $error,
		]);
	}
	
	#[Route(path: '/logout', name: 'app_logout')]
	public function logout(): void {
		throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
	}
	
	#[Route('/register', name: 'app_register')]
	public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response {
		$user = new User();
		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()) {
			/** @var string $plainPassword */
			$plainPassword = $form->get('plainPassword')
				->getData();
			
			// encode the plain password
			$user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
			
			$entityManager->persist($user);
			$entityManager->flush();
			
			// generate a signed url and email it to the user
			$this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, (new TemplatedEmail())->from(new Address('info@zitkino.cz', 'Zit kino bot'))
				->to((string)$user->getEmail())
				->subject('Please Confirm your Email')
				->htmlTemplate('security/confirmation_email.html.twig'));
			
			// do anything else you need here, like send an email
			
			return $security->login($user, 'form_login', 'main');
		}
		
		return $this->render('security/register.html.twig', [
			'registrationForm' => $form,
		]);
	}
	
	#[Route('/verify/email', name: 'app_verify_email')]
	public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response {
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		// validate email confirmation link, sets User::isVerified=true and persists
		try {
			/** @var User $user */
			$user = $this->getUser();
			$this->emailVerifier->handleEmailConfirmation($request, $user);
		} catch(VerifyEmailExceptionInterface $exception) {
			$this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
			
			return $this->redirectToRoute('app_register');
		}
		
		// @TODO Change the redirect on success and handle or remove the flash message in your templates
		$this->addFlash('success', 'Your email address has been verified.');
		
		return $this->redirectToRoute('app_register');
	}
}
