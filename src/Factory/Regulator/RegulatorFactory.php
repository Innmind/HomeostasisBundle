<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\Factory\Regulator;

use Innmind\Homeostasis\{
    StateHistory,
    Actuator,
    Actuator\StrategyDeterminator,
    Regulator\Regulator,
    Factor
};
use Innmind\TimeContinuum\TimeContinuumInterface;
use Innmind\Immutable\Set;

final class RegulatorFactory
{
    public static function make(
        array $factors,
        StateHistory $states,
        TimeContinuumInterface $clock,
        StrategyDeterminator $determinator,
        Actuator $actuator
    ): Regulator {
        $set = new Set(Factor::class);

        foreach ($factors as $factor) {
            $set = $set->add($factor);
        }

        return new Regulator($set, $states, $clock, $determinator, $actuator);
    }
}
