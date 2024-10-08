<?php

use PhpLlm\LlmChain\Chain;
use PhpLlm\LlmChain\Message\Message;
use PhpLlm\LlmChain\Message\MessageBag;
use PhpLlm\LlmChain\OpenAI\Model\Gpt;
use PhpLlm\LlmChain\OpenAI\Model\Gpt\Version;
use PhpLlm\LlmChain\OpenAI\Runtime\OpenAI;
use PhpLlm\LlmChain\StructuredOutput\ResponseFormatFactory;
use PhpLlm\LlmChain\StructuredOutput\SchemaFactory;
use PhpLlm\LlmChain\Tests\StructuredOutput\Data\MathReasoning;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require_once dirname(__DIR__).'/vendor/autoload.php';

$runtime = new OpenAI(HttpClient::create(), getenv('OPENAI_API_KEY'));
$llm = new Gpt($runtime, Version::gpt4oMini());
$responseFormatFactory = new ResponseFormatFactory(SchemaFactory::create());
$serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

$chain = new Chain($llm, responseFormatFactory: $responseFormatFactory, serializer: $serializer);
$messages = new MessageBag(
    Message::forSystem('You are a helpful math tutor. Guide the user through the solution step by step.'),
    Message::ofUser('how can I solve 8x + 7 = -23'),
);
$response = $chain->call($messages, ['output_structure' => MathReasoning::class]);

dump($response);
