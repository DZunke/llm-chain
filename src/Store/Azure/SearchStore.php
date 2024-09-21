<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Store\Azure;

use PhpLlm\LlmChain\Document\Document;
use PhpLlm\LlmChain\Document\Metadata;
use PhpLlm\LlmChain\Document\Vector;
use PhpLlm\LlmChain\Store\VectorStoreInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SearchStore implements VectorStoreInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $endpointUrl,
        private readonly string $apiKey,
        private readonly string $indexName,
        private readonly string $apiVersion,
    ) {
    }

    public function addDocument(Document $document): void
    {
        $this->addDocuments([$document]);
    }

    public function addDocuments(array $documents): void
    {
        $this->request('index', [
            'value' => array_map([$this, 'convertDocumentToIndexableArray'], $documents),
        ]);
    }

    /**
     * @return list<Document>
     */
    public function query(Vector $vector): array
    {
        $result = $this->request('search', [
            'vectorQueries' => [$this->buildVectorQuery($vector)],
        ]);

        return array_map([$this, 'convertArrayToDocument'], $result['value']);
    }

    private function request(string $endpoint, array $payload): array
    {
        $url = sprintf('%s/indexes/%s/docs/%s', $this->endpointUrl, $this->indexName, $endpoint);
        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'query' => ['api-version' => $this->apiVersion],
            'json' => $payload,
        ]);

        return $response->toArray();
    }

    private function convertDocumentToIndexableArray(Document $document): array
    {
        return array_merge([
            'id' => $document->id,
            'vector' => $document->vector->getData(),
        ], $document->metadata->getArrayCopy());
    }

    private function convertArrayToDocument(array $data): Document
    {
        return new Document(
            id: Uuid::fromString($data['id']),
            text: null,
            vector: null,
            metadata: new Metadata($data),
        );
    }

    private function buildVectorQuery(Vector $vector): array
    {
        return [
            'kind' => 'vector',
            'vector' => $vector->getData(),
            'exhaustive' => true,
            'fields' => 'vector',
            'weight' => 0.5,
            'k' => 5,
        ];
    }
}