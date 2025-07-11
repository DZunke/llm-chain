<?php

use PhpLlm\LlmChain\Chain\Chain;
use PhpLlm\LlmChain\Chain\InputProcessor\SystemPromptInputProcessor;
use PhpLlm\LlmChain\Platform\Bridge\OpenAI\GPT;
use PhpLlm\LlmChain\Platform\Bridge\OpenAI\PlatformFactory;
use PhpLlm\LlmChain\Platform\Message\Message;
use PhpLlm\LlmChain\Platform\Message\MessageBag;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload.php';
(new Dotenv())->loadEnv(dirname(__DIR__).'/.env');

if (!$_ENV['OPENAI_API_KEY']) {
    echo 'Please set the OPENAI_API_KEY environment variable.'.\PHP_EOL;
    exit(1);
}

$platform = PlatformFactory::create($_ENV['OPENAI_API_KEY']);
$model = new GPT(GPT::GPT_4O_MINI);

$processor = new SystemPromptInputProcessor('You are Yoda and write like he speaks. But short.');

$chain = new Chain($platform, $model, [$processor]);
$messages = new MessageBag(Message::ofUser('What is the meaning of life?'));
$response = $chain->call($messages);

echo $response->getContent().\PHP_EOL;
