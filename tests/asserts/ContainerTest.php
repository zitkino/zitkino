<?php

namespace Tests;

$container = require __DIR__."/../bootstrap.php";

use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

/**
 * @testCase
 */
class ContainerTest extends TestCase {
    /** @var Container */
    private $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function testParameters() {
        Assert::same("#ba1b0a", $this->container->getParameters()["meta"]["color"]);
    }
}

$test = new ContainerTest($container);
$test->run();
