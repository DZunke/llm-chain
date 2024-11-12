<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Tests\ToolBox;

use PhpLlm\LlmChain\Chain\Input;
use PhpLlm\LlmChain\Exception\MissingModelSupport;
use PhpLlm\LlmChain\LanguageModel;
use PhpLlm\LlmChain\Message\MessageBag;
use PhpLlm\LlmChain\ToolBox\ChainProcessor;
use PhpLlm\LlmChain\ToolBox\ToolBoxInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainProcessor::class)]
#[UsesClass(Input::class)]
#[UsesClass(MessageBag::class)]
#[UsesClass(MissingModelSupport::class)]
class ChainProcessorTest extends TestCase
{
    #[Test]
    public function processInputWithoutRegisteredToolsWillResultInNoOptionChange(): void
    {
        $toolBox = $this->createStub(ToolBoxInterface::class);
        $toolBox->method('getMap')->willReturn([]);

        $llm = $this->createMock(LanguageModel::class);
        $llm->method('supportsToolCalling')->willReturn(true);

        $chainProcessor = new ChainProcessor($toolBox);
        $input = new Input($llm, new MessageBag(), []);

        $chainProcessor->processInput($input);

        self::assertSame([], $input->getOptions());
    }

    #[Test]
    public function processInputWithRegisteredToolsWillResultInOptionChange(): void
    {
        $toolBox = $this->createStub(ToolBoxInterface::class);
        $toolBox->method('getMap')->willReturn(['tool1' => 'tool1', 'tool2' => 'tool2']);

        $llm = $this->createMock(LanguageModel::class);
        $llm->method('supportsToolCalling')->willReturn(true);

        $chainProcessor = new ChainProcessor($toolBox);
        $input = new Input($llm, new MessageBag(), []);

        $chainProcessor->processInput($input);

        self::assertSame(['tools' => ['tool1' => 'tool1', 'tool2' => 'tool2']], $input->getOptions());
    }

    #[Test]
    public function processInputWithUnsupportedToolCallingWillThrowException(): void
    {
        $this->expectException(MissingModelSupport::class);

        $llm = $this->createMock(LanguageModel::class);
        $llm->method('supportsToolCalling')->willReturn(false);

        $chainProcessor = new ChainProcessor($this->createStub(ToolBoxInterface::class));
        $input = new Input($llm, new MessageBag(), []);

        $chainProcessor->processInput($input);
    }
}