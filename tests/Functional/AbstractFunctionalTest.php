<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Tests\Functional;

use Psr\Container\ContainerInterface;
use webignition\BasilRunner\Kernel;

abstract class AbstractFunctionalTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContainerInterface
     */
    protected static $container;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
        $kernel->boot();

        self::$container = $kernel->getContainer();
    }
}
