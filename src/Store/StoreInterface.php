<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Store;

use PhpLlm\LlmChain\Document\Document;

interface StoreInterface
{
    public function addDocument(Document $document): void;

    /**
     * @param list<Document> $documents
     */
    public function addDocuments(array $documents): void;
}
