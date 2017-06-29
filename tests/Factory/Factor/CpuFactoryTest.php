<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\Factory\Factor;

use Innmind\HomeostasisBundle\Factory\Factor\CpuFactory;
use Innmind\Homeostasis\Factor\Cpu;
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Server\Status\Server;
use PHPUnit\Framework\TestCase;

class CpuFactoryTest extends TestCase
{
    public function testMake()
    {
        $factory = new CpuFactory([
            'weight' => 1,
            'polynom' => [
                'intercept' => 1,
                'degrees' => [1 => 2],
            ],
        ]);

        $factor = $factory->make(
            $this->createMock(TimeContinuumInterface::class),
            $this->createMock(Server::class)
        );

        $this->assertInstanceOf(Cpu::class, $factor);
    }
}
