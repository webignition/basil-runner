<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Services;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use webignition\BasilCompilerModels\InvalidSuiteManifestException;
use webignition\BasilCompilerModels\SuiteManifest;
use webignition\BasilCompilerModels\SuiteManifestFactory as BaseSuiteManifestFactory;
use webignition\BasilRunner\Exception\MalformedSuiteManifestException;

class SuiteManifestFactory
{
    private Parser $yamlParser;
    private BaseSuiteManifestFactory $baseSuiteManifestFactory;

    public function __construct(Parser $yamlParser, BaseSuiteManifestFactory $baseSuiteManifestFactory)
    {
        $this->yamlParser = $yamlParser;
        $this->baseSuiteManifestFactory = $baseSuiteManifestFactory;
    }

    public static function createFactory(): self
    {
        return new SuiteManifestFactory(
            new Parser(),
            new BaseSuiteManifestFactory()
        );
    }

    /**
     * @param string $content
     *
     * @return SuiteManifest
     *
     * @throws InvalidSuiteManifestException
     * @throws MalformedSuiteManifestException
     */
    public function createFromString(string $content): SuiteManifest
    {
        try {
            $data = $this->yamlParser->parse($content);
        } catch (ParseException $yamlParseException) {
            throw MalformedSuiteManifestException::createMalformedYamlException($content);
        }

        if (!is_array($data)) {
            throw MalformedSuiteManifestException::createNonArrayContentException($content);
        }

        return $this->baseSuiteManifestFactory->createFromArray($data);
    }
}