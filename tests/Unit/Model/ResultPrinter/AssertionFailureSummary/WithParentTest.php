<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Tests\Unit\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilRunner\Model\ResultPrinter\AssertionFailureSummary\WithParent;
use webignition\BasilRunner\Tests\Unit\AbstractBaseTest;

class WithParentTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(WithParent $withParent, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $withParent->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'withParent' => new WithParent(),
                'expectedRenderedString' => 'with parent:',
            ],
        ];
    }
}
