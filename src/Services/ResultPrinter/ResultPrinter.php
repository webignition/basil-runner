<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Services\ResultPrinter;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util\Printer;
use webignition\BaseBasilTestCase\BasilTestCaseInterface;
use webignition\BasilDomIdentifierFactory\Factory as DomIdentifierFactory;
use webignition\BasilRunner\Model\TestOutput\Step;
use webignition\BasilRunner\Model\TestOutput\Test as TestOutput;
use webignition\BasilRunner\Services\ProjectRootPathProvider;
use webignition\BasilRunner\Services\ResultPrinter\FailedAssertion\SummaryFactory;
use webignition\BasilRunner\Services\ResultPrinter\FailedAssertion\SummaryHandler;
use webignition\BasilRunner\Services\ResultPrinter\Renderer\ExceptionRenderer;
use webignition\BasilRunner\Services\ResultPrinter\Renderer\StatementLineRenderer;
use webignition\BasilRunner\Services\ResultPrinter\Renderer\StepRenderer;
use webignition\BasilRunner\Services\ResultPrinter\Renderer\TestRenderer;

class ResultPrinter extends Printer implements \PHPUnit\TextUI\ResultPrinter
{
    private string $projectRootPath;
    private StepRenderer $stepRenderer;
    private ?TestOutput $currentTestOutput = null;
    private TestRenderer $testRenderer;

    public function __construct($out = null)
    {
        parent::__construct($out);

        $this->projectRootPath = (ProjectRootPathProvider::create())->get();

        $consoleOutputFactory = new ConsoleOutputFactory();

        $this->testRenderer = new TestRenderer($consoleOutputFactory);
        $this->stepRenderer = new StepRenderer(
            $consoleOutputFactory,
            new StatementLineRenderer($consoleOutputFactory),
            new SummaryHandler(
                DomIdentifierFactory::createFactory(),
                new SummaryFactory($consoleOutputFactory)
            ),
            new ExceptionRenderer($consoleOutputFactory)
        );
    }

    /**
     * @inheritDoc
     */
    public function addError(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addError() method.
    }

    /**
     * @inheritDoc
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
        // TODO: Implement addWarning() method.
    }

    /**
     * @inheritDoc
     */
    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        // TODO: Implement addFailure() method.
    }

    /**
     * @inheritDoc
     */
    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addIncompleteTest() method.
    }

    /**
     * @inheritDoc
     */
    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addRiskyTest() method.
    }

    /**
     * @inheritDoc
     */
    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addSkippedTest() method.
    }

    /**
     * @param TestSuite<Test> $suite
     */
    public function startTestSuite(TestSuite $suite): void
    {
        // TODO: Implement startTestSuite() method.
    }

    /**
     * @param TestSuite<Test> $suite
     */
    public function endTestSuite(TestSuite $suite): void
    {
        // TODO: Implement endTestSuite() method.
    }

    /**
     * @inheritDoc
     */
    public function startTest(Test $test): void
    {
        if ($test instanceof BasilTestCaseInterface) {
            $testPath = $test::getBasilTestPath();

            $isNewTest = $this->currentTestOutput instanceof TestOutput
                ? false === $this->currentTestOutput->hasPath($testPath)
                : true;

            if ($isNewTest) {
                $currentTestOutput = new TestOutput($test, $testPath, $this->projectRootPath);
                $this->write($this->testRenderer->render($currentTestOutput));
                $this->writeEmptyLine();

                $this->currentTestOutput = $currentTestOutput;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function endTest(Test $test, float $time): void
    {
        if ($test instanceof BasilTestCaseInterface) {
            $step = new Step($test);
            $this->write($this->stepRenderer->render($step));
            $this->writeEmptyLine();
            $this->writeEmptyLine();
        }
    }

    private function writeEmptyLine(): void
    {
        $this->write("\n");
    }

    public function printResult(TestResult $result): void
    {
        // @todo: Implement in #361
    }
}
