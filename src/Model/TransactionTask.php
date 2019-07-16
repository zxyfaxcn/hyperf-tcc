<?php


namespace Hyperf\Tcc\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int status
 * @property string data
 */
class TransactionTask extends Model
{
    protected $table = 'transaction_task';

    public static function insert(object $data)
    {
        $task = new self();
        $task->status = 1;
        $task->data = serialize($data);
        $result = $task->save();
        if (! $result) {
            throw new \Exception();
        }
        return $task;
    }

    public function getData()
    {
        return unserialize($this->data);
    }

    public function taskComplete()
    {
        $this->status = 0;
        $this->save();
    }
}