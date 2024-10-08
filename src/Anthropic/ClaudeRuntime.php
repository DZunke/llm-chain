<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Anthropic;

interface ClaudeRuntime
{
    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     */
    public function request(array $body): array;
}
