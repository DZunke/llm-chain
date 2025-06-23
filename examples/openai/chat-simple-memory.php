<?php

use PhpLlm\LlmChain\Chain\Chain;
use PhpLlm\LlmChain\Chain\Memory\MemoryProcessor;
use PhpLlm\LlmChain\Chain\Memory\SimpleMemoryEntry;
use PhpLlm\LlmChain\Chain\Memory\Storage\InMemoryStorage;
use PhpLlm\LlmChain\Chain\Memory\Strategy\ExtendSystemPromptStrategy;
use PhpLlm\LlmChain\Platform\Bridge\OpenAI\GPT;
use PhpLlm\LlmChain\Platform\Bridge\OpenAI\PlatformFactory;
use PhpLlm\LlmChain\Platform\Message\Message;
use PhpLlm\LlmChain\Platform\Message\MessageBag;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__, 2).'/vendor/autoload.php';
(new Dotenv())->loadEnv(dirname(__DIR__, 2).'/.env');

if (empty($_ENV['OPENAI_API_KEY'])) {
    echo 'Please set the OPENAI_API_KEY environment variable.'.\PHP_EOL;
    exit(1);
}

$storage = new InMemoryStorage();
$storage->store([new SimpleMemoryEntry('My name is Maurice the Knight of Love!')]); // Store a simple memory entry

$strategy = new ExtendSystemPromptStrategy();

$memoryProcessor = new MemoryProcessor($storage, $strategy);

$platform = PlatformFactory::create($_ENV['OPENAI_API_KEY']);
$model = new GPT(GPT::GPT_41_NANO, [
    'temperature' => 0.5, // default options for the model
]);

$chain = new Chain($platform, $model, [$memoryProcessor], [$memoryProcessor]);
$messages = new MessageBag(
    Message::forSystem('You are a personal assistant and you write in a friendly manner, oriented to help.'),
    Message::ofUser('Sorry, but what was my name again?'),
);

$response = $chain->call($messages);

echo $response->getContent().\PHP_EOL;
