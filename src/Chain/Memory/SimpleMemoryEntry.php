<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\Memory;

/**
 * A simple memory entry that can be used to store and retrieve memory in a chain call. It only can contain a string.
 *
 * @author Denis Zunke <denis.zunke@gmail.com>
 */
final readonly class SimpleMemoryEntry implements MemoryEntryInterface
{
    public function __construct(
        private string $content,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
