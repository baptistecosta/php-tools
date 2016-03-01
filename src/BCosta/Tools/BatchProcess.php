<?php

namespace BCosta\Tools;

/**
 * Class BatchProcess
 * @package BCosta
 * @author Baptiste Costa
 */
class BatchProcess
{
    /**
     * @var int
     */
    protected $batchSize;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var Callable
     */
    protected $count;

    /**
     * @var Callable
     */
    protected $worker;

    /**
     * BatchProcess constructor.
     * @param int $batchSize
     */
    public function __construct($batchSize)
    {
        $this->batchSize = $batchSize;
    }

    /**
     * @param callable $count
     * @return $this
     */
    public function setCount(callable $count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @param callable $worker
     * @return $this
     */
    public function setWorker(callable $worker)
    {
        $this->worker = $worker;

        return $this;
    }

    /**
     * @param Callable $listener
     * @return $this
     */
    public function addListener(callable $listener)
    {
        $this->listeners[] = $listener;

        return $this;
    }

    /**  */
    public function run()
    {
        $count = call_user_func($this->count);
        $iterations = $this->getIterationsCount($count, $this->batchSize);

        for ($i = 0; $i < $iterations; $i++) {
            $offset = $this->getOffset($i, $this->batchSize);
            $rows = call_user_func_array(
                $this->worker,
                [
                    $offset,
                    $this->batchSize,
                ]
            );
            if (!$rows) {
                throw new \Exception("Worker's result set is null");
            }

            foreach ($rows as $row) {
                foreach ($this->listeners as $listener) {
                    $listener($row);
                }
            }
        }
    }

    /**
     * Return the number of batch iteration.
     * @param int $count
     * @param int $batchSize
     * @return int
     */
    private function getIterationsCount($count, $batchSize)
    {
        return (int) ceil($count / $batchSize);
    }

    /**
     * Get current offset.
     *
     * @param int $iteration
     * @param int $batchSize
     * @return int
     */
    private function getOffset($iteration, $batchSize)
    {
        return (int) ($iteration * $batchSize);
    }
}
