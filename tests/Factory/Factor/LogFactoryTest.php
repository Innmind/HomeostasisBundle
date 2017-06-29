<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\Factory\Factor;

use Innmind\HomeostasisBundle\Factory\Factor\LogFactory;
use Innmind\Homeostasis\Factor\Log;
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\LogReader\Reader;
use Innmind\Filesystem\AdapterInterface;
use PHPUnit\Framework\TestCase;

class LogFactoryTest extends TestCase
{
    public function testMake()
    {
        $factory = new LogFactory([
            'weight' => 1,
            'polynom' => [
                'intercept' => 1,
                'degrees' => [1 => 2],
            ],
        ]);

        $factor = $factory->make(
            $this->createMock(TimeContinuumInterface::class),
            $this->createMock(Reader::class),
            $this->createMock(AdapterInterface::class),
            function(){},
            'foo'
        );

        $this->assertInstanceOf(Log::class, $factor);
    }
}
