<?php

namespace App\Command;

use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command {
	protected static $defaultName = 'app:create-user';
	
	private $em;
	
	private $passwordHasher;
	
	public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher) {
		parent::__construct();
		$this->em = $em;
		$this->passwordHasher = $passwordHasher;
	}
	
	protected function configure() {
		$this->setDescription('Registers a new user.')
			->addArgument('email', InputArgument::REQUIRED, 'User email')
			->addArgument('password', InputArgument::REQUIRED, 'User password');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$user = new User();
		$user->setEmail($input->getArgument('email'));
		$hashedPassword = $this->passwordHasher->hashPassword($user, $input->getArgument('password'));
		$user->setPassword($hashedPassword);
		
		$this->em->persist($user);
		$this->em->flush();
		
		$output->writeln('User registered: '.$user->getEmail());
		return Command::SUCCESS;
	}
}
