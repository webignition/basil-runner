<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Tests\Unit\Model\ResultPrinter\Step;

use PHPUnit\Runner\BaseTestRunner;
use webignition\BaseBasilTestCase\BasilTestCaseInterface;
use webignition\BasilModels\DataSet\DataSet;
use webignition\BasilModels\DataSet\DataSetInterface;
use webignition\BasilModels\StatementInterface;
use webignition\BasilParser\ActionParser;
use webignition\BasilParser\AssertionParser;
use webignition\BasilRunner\Model\ResultPrinter\Literal;
use webignition\BasilRunner\Model\ResultPrinter\RenderableInterface;
use webignition\BasilRunner\Model\ResultPrinter\Step\Step;
use webignition\BasilRunner\Model\TestOutput\Status;
use webignition\BasilRunner\Tests\Unit\AbstractBaseTest;

class StepTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(Step $step, string $expectedRenderedStep)
    {
        $this->assertSame($expectedRenderedStep, $step->render());
    }

    public function renderDataProvider(): array
    {
        $actionParser = ActionParser::create();
        $assertionParser = AssertionParser::create();

        return [
            'passed, no statements' => [
                'step' => new Step($this->createBasilTestCase(
                    Status::SUCCESS,
                    'passed step name',
                    []
                )),
                'expectedRenderedStep' => '<icon-success /> <success>passed step name</success>',
            ],
            'failed, no statements' => [
                'step' => new Step($this->createBasilTestCase(
                    Status::FAILURE,
                    'failed step name',
                    []
                )),
                'expectedRenderedStep' => '<icon-failure /> <failure>failed step name</failure>',
            ],
            'unknown, no statements' => [
                'step' => new Step($this->createBasilTestCase(
                    BaseTestRunner::STATUS_ERROR,
                    'unknown step name',
                    []
                )),
                'expectedRenderedStep' => '<icon-unknown /> <failure>unknown step name</failure>',
            ],
            'passed, click statement completed' => [
                'step' => new Step($this->createBasilTestCase(
                    BaseTestRunner::STATUS_PASSED,
                    'passed step name',
                    [
                        $actionParser->parse('click $".selector"'),
                    ]
                )),
                'expectedRenderedStep' =>
                    '<icon-success /> <success>passed step name</success>' . "\n" .
                    '  <icon-success /> click $".selector"'
                ,
            ],
            'passed, has data' => [
                'step' => new Step($this->createBasilTestCase(
                    BaseTestRunner::STATUS_PASSED,
                    'passed step name',
                    [
                        $actionParser->parse('set $".search" to $data.search'),
                        $assertionParser->parse('$page.title matches $data.expected_title_pattern'),
                    ],
                    new DataSet(
                        'data set name',
                        [
                            'search' => 'value1',
                            'expected_title_pattern' => 'value2',
                        ]
                    )
                )),
                'expectedRenderedStep' =>
                    '<icon-success /> <success>passed step name: data set name</success>' . "\n" .
                    '    $search: <comment>value1</comment>' . "\n" .
                    '    $expected_title_pattern: <comment>value2</comment>' . "\n" .
                    "\n" .
                    '  <icon-success /> set $".search" to $data.search' . "\n" .
                    '  <icon-success /> $page.title matches $data.expected_title_pattern'
                ,
            ],
            'failed, has failure statement' => [
                'step' => $this->setFailedStatementOnStep(
                    new Step($this->createBasilTestCase(
                        BaseTestRunner::STATUS_FAILURE,
                        'failed step name',
                        []
                    )),
                    new Literal('failure statement')
                ),
                'expectedRenderedStep' =>
                    '<icon-failure /> <failure>failed step name</failure>' . "\n" .
                    '  failure statement'
                ,
            ],
            'failed, has last exception' => [
                'step' => $this->setLastExceptionOnStep(
                    new Step($this->createBasilTestCase(
                        BaseTestRunner::STATUS_FAILURE,
                        'failed step name',
                        []
                    )),
                    new Literal('last exception')
                ),
                'expectedRenderedStep' =>
                    '<icon-failure /> <failure>failed step name</failure>' . "\n" .
                    '  last exception'
                ,
            ],
            'failed, has failure statement, has last exception' => [
                'step' => $this->setLastExceptionOnStep(
                    $this->setFailedStatementOnStep(
                        new Step($this->createBasilTestCase(
                            BaseTestRunner::STATUS_FAILURE,
                            'failed step name',
                            []
                        )),
                        new Literal('failure statement')
                    ),
                    new Literal('last exception')
                ),
                'expectedRenderedStep' =>
                    '<icon-failure /> <failure>failed step name</failure>' . "\n" .
                    '  failure statement' . "\n" .
                    '  last exception'
                ,
            ],
        ];
    }

    /**
     * @param int $status
     * @param string $name
     * @param StatementInterface[] $handledStatements
     * @param DataSetInterface|null $dataSet
     *
     * @return BasilTestCaseInterface
     */
    private function createBasilTestCase(
        int $status,
        string $name,
        array $handledStatements,
        ?DataSetInterface $dataSet = null
    ): BasilTestCaseInterface {
        $step = \Mockery::mock(BasilTestCaseInterface::class);

        $step
            ->shouldReceive('getStatus')
            ->andReturn($status);

        $step
            ->shouldReceive('getBasilStepName')
            ->andReturn($name);

        $step
            ->shouldReceive('getHandledStatements')
            ->andReturn($handledStatements);

        $step
            ->shouldReceive('getCurrentDataSet')
            ->andReturn($dataSet);

        return $step;
    }

    private function setFailedStatementOnStep(Step $step, RenderableInterface $renderable): Step
    {
        $step->setFailedStatement($renderable);

        return $step;
    }

    private function setLastExceptionOnStep(Step $step, RenderableInterface $renderable): Step
    {
        $step->setLastException($renderable);

        return $step;
    }
}
