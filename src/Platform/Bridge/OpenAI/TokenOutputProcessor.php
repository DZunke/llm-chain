<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Platform\Bridge\OpenAI;

use PhpLlm\LlmChain\Chain\Output;
use PhpLlm\LlmChain\Chain\OutputProcessorInterface;
use PhpLlm\LlmChain\Platform\Response\StreamResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Denis Zunke <denis.zunke@gmail.com>
 */
final class TokenOutputProcessor implements OutputProcessorInterface
{
    public function processOutput(Output $output): void
    {
        if ($output->response instanceof StreamResponse) {
            // Streams have to be handled manually as the tokens are part of the streamed chunks
            return;
        }

        $rawResponse = $output->response->getRawResponse()?->getRawObject();
        if (!$rawResponse instanceof ResponseInterface) {
            return;
        }

        $metadata = $output->response->getMetadata();

        $metadata->add(
            'remaining_tokens',
            (int) $rawResponse->getHeaders(false)['x-ratelimit-remaining-tokens'][0],
        );

        $content = $rawResponse->toArray(false);

        if (!\array_key_exists('usage', $content)) {
            return;
        }

        $metadata->add('prompt_tokens', $content['usage']['prompt_tokens'] ?? null);
        $metadata->add('completion_tokens', $content['usage']['completion_tokens'] ?? null);
        $metadata->add('total_tokens', $content['usage']['total_tokens'] ?? null);
    }
}
