<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\Factory\Regulator;

use Innmind\HomeostasisBundle\Factory\Regulator\RegulatorFactory;
use Innmind\Homeostasis\{
    Factor,
    StateHistory,
    Actuator,
    Actuator\StrategyDeterminator,
    Regulator\Regulator
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use PHPUnit\Framework\TestCase;

class RegulatorFactoryTest extends TestCase
{
    public function testMake()
    {
        $regulator = RegulatorFactory::make(
            [$this->createMock(Factor::class)],
            $this->createMock(StateHistory::class),
            $this->createMock(TimeContinuumInterface::class),
            $this->createMock(StrategyDeterminator::class),
            $this->createMock(Actuator::class)
        );

        $this->assertInstanceOf(Regulator::class, $regulator);
    }
}
