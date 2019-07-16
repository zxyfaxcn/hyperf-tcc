<?php

namespace Hyperf\Tcc;

use Hyperf\Tcc\Model\TransactionTask;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Utils\Context;

/**
 * @property TccInterface[] tcc
 * @property TransactionTask[] task
 */
class DistributedTransaction
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function try(TccInterface $tcc)
    {
        $task = TransactionTask::insert($tcc);
        $this->tcc = array_merge($this->tcc ?? [], [$tcc]);
        $this->task = array_merge($this->task ?? [], [$task]);
        return $tcc->try();
    }

    public function confirm()
    {
        foreach ($this->task as $task) {
            try {
                /** @var TccInterface $tcc */
                $tcc = $task->getData();
                $tcc->confirm();
                $task->taskComplete();
            } catch (\Exception $exception){
                $this->logger->warning($exception->getMessage());
            }
        }
    }

    public function cancel()
    {
        foreach ($this->tcc as $tcc){
            try {
                $tcc->cancel();
            } catch (\Exception $exception){
                $this->logger->warning($exception->getMessage());
            }
        }
    }

    public function __get($name)
    {
        return Context::get($name);
    }

    public function __set($name, $value)
    {
        Context::set($name, $value);
    }
}