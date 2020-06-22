<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilRunner\Model\ResultPrinter\Literal;

class WithParent extends Literal
{
    public function __construct()
    {
        parent::__construct('with parent:');
    }
}
