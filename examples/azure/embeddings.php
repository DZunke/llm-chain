<?php

use PhpLlm\LlmChain\Platform\Bridge\Azure\OpenAI\PlatformFactory;
use PhpLlm\LlmChain\Platform\Bridge\OpenAI\Embeddings;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__, 2).'/vendor/autoload.php';
(new Dotenv())->loadEnv(dirname(__DIR__, 2).'/.env');

if (!$_ENV['AZURE_OPENAI_BASEURL'] || !$_ENV['AZURE_OPENAI_EMBEDDINGS_DEPLOYMENT'] || !$_ENV['AZURE_OPENAI_EMBEDDINGS_API_VERSION'] || !$_ENV['AZURE_OPENAI_KEY']) {
    echo 'Please set the AZURE_OPENAI_BASEURL, AZURE_OPENAI_EMBEDDINGS_DEPLOYMENT, AZURE_OPENAI_EMBEDDINGS_API_VERSION, and AZURE_OPENAI_KEY environment variables.'.\PHP_EOL;
    exit(1);
}

$platform = PlatformFactory::create(
    $_ENV['AZURE_OPENAI_BASEURL'],
    $_ENV['AZURE_OPENAI_EMBEDDINGS_DEPLOYMENT'],
    $_ENV['AZURE_OPENAI_EMBEDDINGS_API_VERSION'],
    $_ENV['AZURE_OPENAI_KEY'],
);
$embeddings = new Embeddings();

$response = $platform->request($embeddings, <<<TEXT
    Once upon a time, there was a country called Japan. It was a beautiful country with a lot of mountains and rivers.
    The people of Japan were very kind and hardworking. They loved their country very much and took care of it. The
    country was very peaceful and prosperous. The people lived happily ever after.
    TEXT);

echo 'Dimensions: '.$response->asVectors()[0]->getDimensions().\PHP_EOL;
