<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\ToolBox\Event;

use PhpLlm\LlmChain\Chain\ToolBox\ToolCallResult;
use PhpLlm\LlmChain\Model\Response\ResponseInterface;

final class ToolCallsExecuted
{
    /**
     * @var ToolCallResult[]
     */
    public readonly array $toolCallResults;
    public ResponseInterface $response;

    public function __construct(ToolCallResult ...$toolCallResults)
    {
        $this->toolCallResults = $toolCallResults;
    }

    public function hasResponse(): bool
    {
        return isset($this->response);
    }
}