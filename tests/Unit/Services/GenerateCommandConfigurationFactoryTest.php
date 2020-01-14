<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use webignition\BasilRunner\Model\GenerateCommandConfiguration;
use webignition\BasilRunner\Services\GenerateCommandConfigurationFactory;
use webignition\BasilRunner\Services\ProjectRootPathProvider;
use webignition\BasilRunner\Tests\Unit\AbstractBaseTest;

class GenerateCommandConfigurationFactoryTest extends AbstractBaseTest
{
    /**
     * @var GenerateCommandConfigurationFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new GenerateCommandConfigurationFactory(
            new ProjectRootPathProvider()
        );
    }

    /**
     * @dataProvider createFromTypedInputSuccessDataProvider
     */
    public function testCreate(
        string $rawSource,
        string $rawTarget,
        string $baseClass,
        GenerateCommandConfiguration $expectedConfiguration
    ) {
        $this->assertEquals($expectedConfiguration, $this->factory->create($rawSource, $rawTarget, $baseClass));
    }

    public function createFromTypedInputSuccessDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'source and target a resolve to absolute paths' => [
                'rawSource' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                'rawTarget' => 'tests/build/target',
                'baseClass' => TestCase::class,
                'expectedConfiguration' => new GenerateCommandConfiguration(
                    $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    $root . '/tests/build/target',
                    TestCase::class
                ),
            ],
        ];
    }
}
