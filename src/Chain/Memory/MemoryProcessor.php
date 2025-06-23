<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\Memory;

use PhpLlm\LlmChain\Chain\Input;
use PhpLlm\LlmChain\Chain\InputProcessorInterface;
use PhpLlm\LlmChain\Chain\Output;
use PhpLlm\LlmChain\Chain\OutputProcessorInterface;

/**
 * @author Denis Zunke <denis.zunke@gmail.com>
 */
final readonly class MemoryProcessor implements InputProcessorInterface, OutputProcessorInterface
{
    public function __construct(
        private MemoryStorageInterface $storage,
        private MemoryStrategyInterface $strategy,
    ) {
    }

    public function processInput(Input $input): void
    {
        $options = $input->getOptions();
        if (\array_key_exists('memory_active', $options) && false === $options['memory_active']) {
            // The option allows to temporarily disable the memory for a specific chain call.
            return;
        }

        $input->messages = $this->strategy->apply(
            $input->messages,
            $this->storage->load($options),
            $options,
        );
    }

    public function processOutput(Output $output): void
    {
        $options = $output->options;
        if (\array_key_exists('memory_active', $options) && false === $options['memory_active']) {
            // The option allows to temporarily disable the memory for a specific chain call.
            return;
        }

        $memory = $this->strategy->extract($output->messages, $output->response, $options);
        if (0 === \count($memory)) {
            return;
        }

        $this->storage->store($memory, $options);

        dump('After Chain Call');
        dump($memory);
    }
}
