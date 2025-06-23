<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\Memory\Storage;

use PhpLlm\LlmChain\Chain\Memory\MemoryEntryInterface;
use PhpLlm\LlmChain\Chain\Memory\MemoryStorageInterface;

/**
 * A simple in-memory storage for handling a single active conversation. This storage is not persistent and will
 * only store the memory entries for the current php runtime.
 *
 * @author Denis Zunke <denis.zunke@gmail.com>
 */
class InMemoryStorage implements MemoryStorageInterface
{
    /**
     * @var list<MemoryEntryInterface>
     */
    private array $storage = [];

    public function load(array $options = []): array
    {
        return $this->storage;
    }

    public function store(array $memory, array $options = []): void
    {
        if (empty($memory)) {
            return;
        }

        foreach ($memory as $entry) {
            if (!$entry instanceof MemoryEntryInterface) {
                continue;
            }

            if (!\in_array($entry, $this->storage, true)) {
                $this->storage[] = $entry;
            }
        }
    }

    public function remove(MemoryEntryInterface $memoryEntry, array $options = []): void
    {
        if (0 === \count($this->storage)) {
            return;
        }

        $this->storage = array_filter(
            $this->storage,
            static fn (MemoryEntryInterface $entry) => $entry !== $memoryEntry
        );
    }

    public function clear(array $options = []): void
    {
        $this->storage = [];
    }
}
