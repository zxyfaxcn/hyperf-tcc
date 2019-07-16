<?php

namespace Hyperf\Tcc\Listener;

use Hyperf\Database\Events\TransactionCommitted;
use Hyperf\Database\Events\TransactionRolledBack;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Tcc\DistributedTransaction;

/**
 * @Listener
 */
class TransactionListener implements ListenerInterface
{
    /**
     * @var DistributedTransaction
     */
    protected $distributedTransaction;

    public function __construct(DistributedTransaction $distributedTransaction)
    {
        $this->distributedTransaction = $distributedTransaction;
    }

    public function listen(): array
    {
        return [
            TransactionCommitted::class,
            TransactionRolledBack::class,
        ];
    }

    /**
     * @param TransactionCommitted|TransactionRolledBack $event
     */
    public function process(object $event)
    {
        if ($event->connection->transactionLevel()) {
            return;
        }
        switch (get_class($event)){
            case TransactionCommitted::class:
                $this->distributedTransaction->confirm();
                break;
            case TransactionRolledBack::class:
                $this->distributedTransaction->cancel();
                break;
        }
    }
}