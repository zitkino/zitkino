<?php
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    //$_SERVER['SERVER_PORT'] = '443';
}

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

// Add Nette
require_once __DIR__ . '/libs/nette/nette.phar';

// Let bootstrap create Dependency Injection container.
$configurator = new Nette\Configurator;

// Enable Nette Debugger for error visualisation & logging
$configurator->setDebugMode(TRUE);
//$configurator->setDebugMode(FALSE);
$configurator->enableDebugger(__DIR__ . '/_log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/_temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
        ->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/app/config.neon', $configurator::AUTO);
//$configurator->addConfig(__DIR__ . '/app/config.local.neon', $configurator::NONE); // none section
$container = $configurator->createContainer();

// Run application.
$container->getService('application')->run();
return $container;
?>
