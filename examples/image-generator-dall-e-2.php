<?php

use PhpLlm\LlmChain\Bridge\OpenAI\DallE;
use PhpLlm\LlmChain\Bridge\OpenAI\PlatformFactory;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload.php';
(new Dotenv())->loadEnv(dirname(__DIR__).'/.env');

if (empty($_ENV['OPENAI_API_KEY'])) {
    echo 'Please set the OPENAI_API_KEY environment variable.'.PHP_EOL;
    exit(1);
}

$platform = PlatformFactory::create($_ENV['OPENAI_API_KEY']);

$response = $platform->request(
    model: new DallE(),
    input: 'A cartoon-style elephant with a long trunk and large ears.',
    options: [
        'version' => DallE::DALL_E_2, // Utilize Dall-E 2 version
        'response_format' => 'url', // Generate response as URL
        'n' => 2, // Generate multiple images for example
    ],
);

foreach ($response->getContent() as $index => $image) {
    echo 'Image '.$index.': '.$image->url.PHP_EOL;
}