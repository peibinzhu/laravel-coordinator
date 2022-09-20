<?php

declare(strict_types=1);

namespace PeibinLaravel\Coordinator;

class Constants
{
    /**
     * Swoole onWorkerStart event.
     */
    public const WORKER_START = 'workerStart';

    /**
     * Swoole onWorkerExit event.
     */
    public const WORKER_EXIT = 'workerExit';
}
