<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilRunner\Model\ResultPrinter\Comment;
use webignition\BasilRunner\Model\ResultPrinter\RenderableInterface;

class Property implements RenderableInterface
{
    private string $key;
    private Comment $value;
    private string $padding;

    public function __construct(string $key, string $value, string $padding = '')
    {
        $this->key = $key;
        $this->value = new Comment($value);
        $this->padding = $padding;
    }

    public function render(): string
    {
        return sprintf(
            '- %s: %s%s',
            $this->key,
            $this->padding,
            $this->value->render()
        );
    }
}
