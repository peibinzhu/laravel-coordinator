<?php

declare(strict_types=1);

namespace PeibinLaravel\Coordinator\Contracts;

interface ChannelInterface
{
    /**
     * @param mixed $data
     * @param float $timeout [optional] = -1
     * @return bool
     */
    public function push(mixed $data, float $timeout = -1): bool;

    /**
     * @param float $timeout seconds [optional] = -1
     * @return mixed when pop failed, return false
     */
    public function pop(float $timeout = -1): mixed;

    /**
     * Swow: When the channel is closed, all the data in it will be destroyed.
     * Swoole: When the channel is closed, the data in it can still be popped out, but push behavior will no longer succeed.
     */
    public function close(): bool;

    public function getCapacity(): int;

    public function getLength(): int;

    public function isAvailable(): bool;

    public function hasProducers(): bool;

    public function hasConsumers(): bool;

    public function isEmpty();

    public function isFull();

    public function isReadable(): bool;

    public function isWritable(): bool;

    public function isClosing(): bool;

    public function isTimeout(): bool;
}
