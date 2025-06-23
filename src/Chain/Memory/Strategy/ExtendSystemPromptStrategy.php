<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\Memory\Strategy;

use PhpLlm\LlmChain\Chain\Memory\MemoryEntryInterface;
use PhpLlm\LlmChain\Chain\Memory\MemoryStrategyInterface;
use PhpLlm\LlmChain\Platform\Message\Message;
use PhpLlm\LlmChain\Platform\Message\MessageBagInterface;
use PhpLlm\LlmChain\Platform\Message\SystemMessage;
use PhpLlm\LlmChain\Platform\Response\ResponseInterface;

final readonly class ExtendSystemPromptStrategy implements MemoryStrategyInterface
{
    public function apply(MessageBagInterface $messages, array $memory, array $options = []): MessageBagInterface
    {
        if (0 === \count($memory)) {
            return $messages;
        }

        // Extract the system prompt from the messages
        $systemMessage = $messages->getSystemMessage();
        if (!$systemMessage instanceof SystemMessage) {
            return $messages;
        }

        // Create a new system message with the memory entries
        $memoryContent = $systemMessage->content.\PHP_EOL.'# Conversation Memory Entries'.\PHP_EOL;
        foreach ($memory as $entry) {
            if (!$entry instanceof MemoryEntryInterface) {
                continue;
            }

            $memoryContent .= '- '.$entry->getContent().\PHP_EOL;
        }

        return $messages->withoutSystemMessage()->prepend(Message::forSystem($memoryContent));
    }

    public function extract(MessageBagInterface $messages, ResponseInterface $response, array $options = []): array
    {
        return [];
    }
}
