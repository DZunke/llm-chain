<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Chain\Memory;

/**
 * The interface represents a memory storage to store different memory entries for a chain call to remember in any
 * further call. This can have different custom storage backend but will be utilized by the chain memory to work
 * with remembered information from the chain usage based on the applied memory strategies.
 *
 * @author Denis Zunke <denis.zunke@gmail.com>
 */
interface MemoryStorageInterface
{
    /**
     * Loading the memory of the current chain call. This should be called before the chain call is executed and
     * then be delivered to the memory strategies that should process the memory and so decide with memory
     * entries should be added to the chain call.
     *
     * @param array<string, mixed> $options a list of options from the chain call
     *
     * @return list<MemoryEntryInterface>
     */
    public function load(array $options = []): array;

    /**
     * After the chain call the memory strategies determine which memory entries should be stored. This method
     * will get this list of memory entries and store them in the fitting storage. It should not be needed to again
     * determine if the storage is suitable but can be for the specific memory entry.
     *
     * @param list<MemoryEntryInterface> $memory
     * @param array<string, mixed>       $options a list of options from the chain call
     */
    public function store(array $memory, array $options = []): void;

    /**
     * Remove a specific memory entry from the storage if the specific memory entry is qualified to be forgotten from
     * the memory storage for this chain call. This is usually called by the memory strategies after the chain call
     * has been executed.
     *
     * @param array<string, mixed> $options a list of options from the chain call
     */
    public function remove(MemoryEntryInterface $memoryEntry, array $options = []): void;

    /**
     * Clear the memory storage for the current chain call. If the complete memory should be cleared, this method will
     * make the chain forget all memory entries that are assigned to it after the latest chain call was done.
     *
     * @param array<string, mixed> $options a list of options from the chain call
     */
    public function clear(array $options = []): void;
}
