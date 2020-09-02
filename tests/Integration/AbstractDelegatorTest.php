<?php

declare(strict_types=1);

namespace webignition\BasilRunnerDelegator\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use webignition\TcpCliProxyClient\Client;
use webignition\YamlDocumentSetParser\Parser;

abstract class AbstractDelegatorTest extends TestCase
{
    private Client $compilerClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compilerClient = Client::createFromHostAndPort('localhost', 9000);
    }

    protected function compile(string $source, string $target, string $manifestPath): OutputInterface
    {
        $output = new BufferedOutput();
        $compilerClient = $this->compilerClient->withOutput($output);

        $compilerClient->request(sprintf(
            './compiler --source=%s --target=%s 1>%s',
            $source,
            $target,
            $manifestPath
        ));

        return $output;
    }

    protected function removeCompiledArtifacts(string $target, string $manifestPath): OutputInterface
    {
        $output = new BufferedOutput();
        $compilerClient = $this->compilerClient->withOutput($output);

        $compilerClient->request(sprintf('rm %s', $manifestPath));
        $compilerClient->request(sprintf('rm %s/*.php', $target));

        return $output;
    }

    /**
     * @param array<mixed> $expectedOutputDocuments
     * @param string $content
     */
    protected static function assertDelegatorOutput(array $expectedOutputDocuments, string $content): void
    {
        $yamlDocumentSetParser = new Parser();
        $outputDocuments = $yamlDocumentSetParser->parse($content);

        self::assertSame($expectedOutputDocuments, $outputDocuments);
    }

    public function delegatorDataProvider(): array
    {
        return [
            'index open form open chrome firefox' => [
                'source' => '/app/source/TestSuite/index-open-form-open-chrome-firefox.yml',
                'target' => '/app/tests',
                'manifestPath' => '/app/manifests/manifest.yml',
                'expectedOutputDocuments' => [
                    [
                        'type' => 'test',
                        'path' => '/app/source/Test/index-open-chrome.yml',
                        'config' => [
                            'browser' => 'chrome',
                            'url' => 'http://nginx/index.html',
                        ],
                    ],
                    [
                        'type' => 'step',
                        'name' => 'verify page is open',
                        'status' => 'passed',
                        'statements' => [
                            [
                                'type' => 'assertion',
                                'source' => '$page.url is "http://nginx/index.html"',
                                'status' => 'passed',
                            ],
                            [
                                'type' => 'assertion',
                                'source' => '$page.title is "Test fixture web server default document"',
                                'status' => 'passed',
                            ],
                        ],
                    ],
                    [
                        'type' => 'test',
                        'path' => '/app/source/Test/index-open-firefox.yml',
                        'config' => [
                            'browser' => 'firefox',
                            'url' => 'http://nginx/index.html',
                        ],
                    ],
                    [
                        'type' => 'step',
                        'name' => 'verify page is open',
                        'status' => 'passed',
                        'statements' => [
                            [
                                'type' => 'assertion',
                                'source' => '$page.url is "http://nginx/index.html"',
                                'status' => 'passed',
                            ],
                            [
                                'type' => 'assertion',
                                'source' => '$page.title is "Test fixture web server default document"',
                                'status' => 'passed',
                            ],
                        ],
                    ],
                    [
                        'type' => 'test',
                        'path' => '/app/source/Test/form-open-chrome.yml',
                        'config' => [
                            'browser' => 'chrome',
                            'url' => 'http://nginx/form.html',
                        ],
                    ],
                    [
                        'type' => 'step',
                        'name' => 'verify page is open',
                        'status' => 'passed',
                        'statements' => [
                            [
                                'type' => 'assertion',
                                'source' => '$page.url is "http://nginx/form.html"',
                                'status' => 'passed',
                            ],
                            [
                                'type' => 'assertion',
                                'source' => '$page.title is "Form"',
                                'status' => 'passed',
                            ],
                        ],
                    ],
                    [
                        'type' => 'test',
                        'path' => '/app/source/Test/form-open-firefox.yml',
                        'config' => [
                            'browser' => 'firefox',
                            'url' => 'http://nginx/form.html',
                        ],
                    ],
                    [
                        'type' => 'step',
                        'name' => 'verify page is open',
                        'status' => 'passed',
                        'statements' => [
                            [
                                'type' => 'assertion',
                                'source' => '$page.url is "http://nginx/form.html"',
                                'status' => 'passed',
                            ],
                            [
                                'type' => 'assertion',
                                'source' => '$page.title is "Form"',
                                'status' => 'passed',
                            ],
                        ],
                    ],
                ],
            ],
            'index failing chrome' => [
                'source' => '/app/source/FailingTest/index-failing.yml',
                'target' => '/app/tests',
                'manifestPath' => '/app/manifests/manifest.yml',
                'expectedOutputDocuments' => [
                    [
                        'type' => 'test',
                        'path' => '/app/source/FailingTest/index-failing.yml',
                        'config' => [
                            'browser' => 'chrome',
                            'url' => 'http://nginx/index.html',
                        ],
                    ],
                    [
                        'type' => 'step',
                        'name' => 'verify page is open',
                        'status' => 'passed',
                        'statements' => [
                            [
                                'type' => 'assertion',
                                'source' => '$page.url is "http://nginx/index.html"',
                                'status' => 'passed',
                            ],
                        ],
                    ],
                    [
                        'type' => 'step',
                        'name' => 'verify links are present',
                        'status' => 'failed',
                        'statements' => [
                            [
                                'type' => 'assertion',
                                'source' => '$"a[id=link-to-assertions]" not-exists',
                                'status' => 'failed',
                                'summary' => [
                                    'operator' => 'not-exists',
                                    'source' => [
                                        'type' => 'node',
                                        'body' => [
                                            'type' => 'element',
                                            'identifier' => [
                                                'source' => '$"a[id=link-to-assertions]"',
                                                'properties' => [
                                                    'type' => 'css',
                                                    'locator' => 'a[id=link-to-assertions]',
                                                    'position' => 1,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
