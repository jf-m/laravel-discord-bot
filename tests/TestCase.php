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
            $this->assertEquals($expectedValue, $actualArray[$expectedKey]);
        }
    }
}
