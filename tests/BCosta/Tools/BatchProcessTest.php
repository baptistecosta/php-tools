<?php

namespace BCosta\Tools;

/**
 * Class BatchProcessTest
 * @package BCosta
 */
class BatchProcessTest extends \PHPUnit_Framework_TestCase
{
    const BATCH_SIZE = 20;

    /**
     * @var array
     */
    private $countries;

    protected function setUp()
    {
        $this->countries = json_decode(
            file_get_contents(
                sprintf('%s/../../../resources/countries.json', __DIR__)
            ),
            true
        );
    }

    /**  */
    public function testRun()
    {
        $batchProcess = new BatchProcess(self::BATCH_SIZE);
        $batchProcess
            ->setCount(function () {
                return count($this->countries);
            })
            ->setWorker(function ($offset, $batchSize) {
                return array_slice(
                    $this->countries,
                    $offset,
                    $batchSize
                );
            })
            ->addListener(function ($row) {
                // Do something
//                echo $row['code'] . "\n";
            })
            ->run();
    }
}