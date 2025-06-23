<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\Memory;

/**
 * @author Denis Zunke <denis.zunke@gmail.com>
 */
interface MemoryEntryInterface
{
    /**
     * Convert the memory entry to a chain message that can be utilized in the chain call. This is usually
     * a message that is added to the chain call as a system message, a remembered tool call or a past assistant and
     * user message.
     */
    public function getContent(): string;
}
