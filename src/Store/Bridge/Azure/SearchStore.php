<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Store\Bridge\Azure;

use PhpLlm\LlmChain\Platform\Vector\NullVector;
use PhpLlm\LlmChain\Platform\Vector\Vector;
use PhpLlm\LlmChain\Store\Document\Metadata;
use PhpLlm\LlmChain\Store\Document\VectorDocument;
use PhpLlm\LlmChain\Store\VectorStoreInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final readonly class SearchStore implements VectorStoreInterface
{
    /**
     * @param string $vectorFieldName The name of the field int the index that contains the vector
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $endpointUrl,
        #[\SensitiveParameter] private string $apiKey,
        private string $indexName,
        private string $apiVersion,
        private string $vectorFieldName = 'vector',
    ) {
    }

    public function add(VectorDocument ...$documents): void
    {
        $this->request('index', [
            'value' => array_map([$this, 'convertToIndexableArray'], $documents),
        ]);
    }

    public function query(Vector $vector, array $options = [], ?float $minScore = null): array
    {
        $result = $this->request('search', [
            'vectorQueries' => [$this->buildVectorQuery($vector)],
        ]);

        return array_map([$this, 'convertToVectorDocument'], $result['value']);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function request(string $endpoint, array $payload): array
    {
        $url = \sprintf('%s/indexes/%s/docs/%s', $this->endpointUrl, $this->indexName, $endpoint);
        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'api-key' => $this->apiKey,
            ],
            'query' => ['api-version' => $this->apiVersion],
            'json' => $payload,
        ]);

        return $response->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    private function convertToIndexableArray(VectorDocument $document): array
    {
        return array_merge([
            'id' => $document->id,
            $this->vectorFieldName => $document->vector->getData(),
        ], $document->metadata->getArrayCopy());
    }

    /**
     * @param array<string, mixed> $data
     */
    private function convertToVectorDocument(array $data): VectorDocument
    {
        return new VectorDocument(
            id: Uuid::fromString($data['id']),
            vector: !\array_key_exists($this->vectorFieldName, $data) || null === $data[$this->vectorFieldName]
                ? new NullVector()
                : new Vector($data[$this->vectorFieldName]),
            metadata: new Metadata($data),
        );
    }

    /**
     * @return array{
     *     kind: 'vector',
     *     vector: float[],
     *     exhaustive: true,
     *     fields: non-empty-string,
     *     weight: float,
     *     k: int,
     * }
     */
    private function buildVectorQuery(Vector $vector): array
    {
        return [
            'kind' => 'vector',
            'vector' => $vector->getData(),
            'exhaustive' => true,
            'fields' => $this->vectorFieldName,
            'weight' => 0.5,
            'k' => 5,
        ];
    }
}
