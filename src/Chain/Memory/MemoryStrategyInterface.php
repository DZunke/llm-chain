<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\Memory;

use PhpLlm\LlmChain\Platform\Message\MessageBagInterface;
use PhpLlm\LlmChain\Platform\Response\ResponseInterface;

/**
 * For working with the memory in the chain, a memory strategy can be used to define how the memory is applied
 * to a chain call and which memory entries to store after the chain call. This allows to define and combine different
 * strategies with for example different strategies for long term or short term memory that are given from users
 * or assistants.
 *
 * @author Denis Zunke <denis.zunke@gmail.com>
 */
interface MemoryStrategyInterface
{
    /**
     * Enrich the given messages with the memory entries that are stored in the memory. This can be used to add
     * past user and assistant messages, tool calls or system messages to the chain call.
     *
     * @param MessageBagInterface        $messages the message bag in the state before the memory handling
     * @param list<MemoryEntryInterface> $memory   the memory entries that could be utilized to enrich the messages
     * @param array<string, mixed>       $options  runtime options for the strategy, based on the chain call
     *
     * @return MessageBagInterface a new message bag with the collection of chain messages after memory handling
     */
    public function apply(MessageBagInterface $messages, array $memory, array $options = []): MessageBagInterface;

    /**
     * Extract the memory entries from the response of the chain call. This should determine which content of the
     * conversation is worth to be remembered and stored in the memory storage for future chain calls.
     *
     * @param MessageBagInterface  $messages the message bag of the chain call that was executed
     * @param ResponseInterface    $response the response of the chain call that was executed
     * @param array<string, mixed> $options  runtime options for the strategy, based on the chain call
     *
     * @return array<MemoryEntryInterface> a list of memory entries that should be stored in the memory storage
     */
    public function extract(MessageBagInterface $messages, ResponseInterface $response, array $options = []): array;
}
