<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Tests\Message;

use PhpLlm\LlmChain\Message\Message;
use PhpLlm\LlmChain\Response\ToolCall;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Message::class)]
#[UsesClass(ToolCall::class)]
#[Small]
final class MessageTest extends TestCase
{
    #[Test]
    public function createSystemMessage(): void
    {
        $message = Message::forSystem('My amazing system prompt.');

        self::assertSame('My amazing system prompt.', $message->content);
        self::assertTrue($message->isSystem());
        self::assertFalse($message->isAssistant());
        self::assertFalse($message->isUser());
        self::assertFalse($message->isToolCall());
        self::assertFalse($message->hasToolCalls());
    }

    #[Test]
    public function createAssistantMessage(): void
    {
        $message = Message::ofAssistant('It is time to sleep.');

        self::assertSame('It is time to sleep.', $message->content);
        self::assertFalse($message->isSystem());
        self::assertTrue($message->isAssistant());
        self::assertFalse($message->isUser());
        self::assertFalse($message->isToolCall());
        self::assertFalse($message->hasToolCalls());
    }

    #[Test]
    public function createAssistantMessageWithToolCalls(): void
    {
        $toolCalls = [
            new ToolCall('call_123456', 'my_tool', ['foo' => 'bar']),
            new ToolCall('call_456789', 'my_faster_tool'),
        ];
        $message = Message::ofAssistant(toolCalls: $toolCalls);

        self::assertCount(2, $message->toolCalls);
        self::assertFalse($message->isSystem());
        self::assertTrue($message->isAssistant());
        self::assertFalse($message->isUser());
        self::assertFalse($message->isToolCall());
        self::assertTrue($message->hasToolCalls());
    }

    #[Test]
    public function createUserMessage(): void
    {
        $message = Message::ofUser('Hi, my name is John.');

        self::assertSame('Hi, my name is John.', $message->content);
        self::assertFalse($message->isSystem());
        self::assertFalse($message->isAssistant());
        self::assertTrue($message->isUser());
        self::assertFalse($message->isToolCall());
        self::assertFalse($message->hasToolCalls());
    }

    #[Test]
    public function createToolCallMessage(): void
    {
        $toolCall = new ToolCall('call_123456', 'my_tool', ['foo' => 'bar']);
        $message = Message::ofToolCall($toolCall, 'Foo bar.');

        self::assertSame('Foo bar.', $message->content);
        self::assertCount(1, $message->toolCalls);
        self::assertFalse($message->isSystem());
        self::assertFalse($message->isAssistant());
        self::assertFalse($message->isUser());
        self::assertTrue($message->isToolCall());
        self::assertTrue($message->hasToolCalls());
    }

    #[DataProvider('provideJsonScenarios')]
    #[Test]
    public function jsonSerialize(Message $message, array $expected): void
    {
        self::assertSame($expected, $message->jsonSerialize());
    }

    public static function provideJsonScenarios(): array
    {
        $toolCall1 = new ToolCall('call_123456', 'my_tool', ['foo' => 'bar']);
        $toolCall2 = new ToolCall('call_456789', 'my_faster_tool');

        return [
            'system' => [
                Message::forSystem('My amazing system prompt.'),
                [
                    'role' => 'system',
                    'content' => 'My amazing system prompt.',
                ],
            ],
            'assistant' => [
                Message::ofAssistant('It is time to sleep.'),
                [
                    'role' => 'assistant',
                    'content' => 'It is time to sleep.',
                ],
            ],
            'assistant_with_tool_calls' => [
                Message::ofAssistant(toolCalls: [$toolCall1, $toolCall2]),
                [
                    'role' => 'assistant',
                    'tool_calls' => [$toolCall1, $toolCall2],
                ],
            ],
            'user' => [
                Message::ofUser('Hi, my name is John.'),
                [
                    'role' => 'user',
                    'content' => 'Hi, my name is John.',
                ],
            ],
            'tool_call' => [
                Message::ofToolCall($toolCall1, 'Foo bar.'),
                [
                    'role' => 'tool',
                    'content' => 'Foo bar.',
                    'tool_call_id' => 'call_123456',
                ],
            ],
        ];
    }
}
