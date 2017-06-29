<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\Factory\Factor;

use Innmind\HomeostasisBundle\Exception\LogicException;
use Innmind\Homeostasis\{
    Sensor\Measure\Weight,
    Factor\Cpu
};
use Innmind\Math\{
    Algebra\Number\Number,
    Algebra\Integer,
    Polynom\Polynom
};
use Innmind\Server\Status\Server;
use Innmind\TimeContinuum\TimeContinuumInterface;

final class CpuFactory
{
    private $weight;
    private $polynom;

    public function __construct(array $config)
    {
        if (
            !isset($config['weight']) ||
            !isset($config['polynom'])
        ) {
            throw new LogicException;
        }

        $this->weight = new Weight(new Number($config['weight']));
        $polynom = new Polynom;

        if (isset($config['polynom']['intercept'])) {
            $polynom = new Polynom(new Number($config['polynom']['intercept']));
        }

        foreach ($config['polynom']['degrees'] ?? [] as $degree => $coeff) {
            $polynom = $polynom->withDegree(
                new Integer($degree),
                new Number($coeff)
            );
        }

        $this->polynom = $polynom;
    }

    public function make(TimeContinuumInterface $clock, Server $server): Cpu
    {
        return new Cpu($clock, $server, $this->weight, $this->polynom);
    }
}
