<?php

declare(strict_types=1);

namespace PeibinLaravel\Coordinator;

use Closure;
use PeibinLaravel\Contracts\StdoutLoggerInterface;
use Swoole\Coroutine;
use Throwable;

class Timer
{
    public const STOP = 'stop';

    private array $closures = [];

    private int $id = 0;

    public function __construct(private ?StdoutLoggerInterface $logger = null)
    {
    }

    public function after(float $timeout, Closure $closure, string $identifier = Constants::WORKER_EXIT): int
    {
        $id = ++$this->id;
        $this->closures[$id] = true;
        Coroutine::create(function () use ($timeout, $closure, $identifier, $id) {
            try {
                $isClosing = CoordinatorManager::until($identifier)->yield($timeout);
                if (isset($this->closures[$id])) {
                    $closure($isClosing);
                }
            } finally {
                unset($this->closures[$id]);
            }
        });
        return $id;
    }

    public function tick(float $timeout, Closure $closure, string $identifier = Constants::WORKER_EXIT): int
    {
        $id = ++$this->id;
        $this->closures[$id] = true;
        Coroutine::create(function () use ($timeout, $closure, $identifier, $id) {
            try {
                while (true) {
                    $isClosing = CoordinatorManager::until($identifier)->yield($timeout);
                    if (!isset($this->closures[$id])) {
                        break;
                    }

                    try {
                        $result = $closure($isClosing);
                        if ($result === self::STOP || $isClosing) {
                            break;
                        }
                    } catch (Throwable $exception) {
                        $this->logger?->error((string)$exception);
                    }
                }
            } finally {
                unset($this->closures[$id]);
            }
        });
        return $id;
    }

    public function clear(int $id): void
    {
        unset($this->closures[$id]);
    }

    public function clearAll(): void
    {
        $this->closures = [];
    }
}
