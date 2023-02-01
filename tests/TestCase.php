<?php
declare(strict_types=1);

namespace Nwilging\LaravelDiscordBotTests;


class TestCase extends \Orchestra\Testbench\TestCase
{
    public function tearDown(): void
    {
        if (class_exists('Mockery')) {
            if ($container = \Mockery::getContainer()) {
                $this->addToAssertionCount($container->mockery_getExpectationCount());
            }

            \Mockery::close();
        }

        parent::tearDown();
    }

    public function assertArraySubset($expectedArraySubset, $actualArray): void
    {
        foreach ($expectedArraySubset as $expectedKey => $expectedValue) {
            $this->assertArrayHasKey($expectedKey, $actualArray, 'Expected key `'. $expectedKey . '` not found in array ' . json_encode($actualArray));
            $this->assertEquals($expectedValue, $actualArray[$expectedKey]);
        }
    }
}
