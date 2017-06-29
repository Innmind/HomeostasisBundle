<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\Log;

use Innmind\HomeostasisBundle\Log\Watcher;
use Innmind\LogReader\{
    Log,
    Log\Attribute,
    Log\Attribute\Symfony\Level
};
use Innmind\TimeContinuum\PointInTimeInterface;
use Innmind\Immutable\{
    Map,
    Str
};
use PHPUnit\Framework\TestCase;

class WatcherTest extends TestCase
{
    public function testWhenNoLevel()
    {
        $watch = new Watcher('critical');

        $this->assertFalse($watch(new Log(
            $this->createMock(PointInTimeInterface::class),
            new Str(''),
            new Map('string', Attribute::class)
        )));
    }

    public function testNonWatchedLevel()
    {
        $watch = new Watcher('critical');

        $this->assertFalse($watch(new Log(
            $this->createMock(PointInTimeInterface::class),
            new Str(''),
            (new Map('string', Attribute::class))
                ->put('level', new Level('INFO'))
        )));
    }

    public function testWatchedLevel()
    {
        $watch = new Watcher('critical');

        $this->assertTrue($watch(new Log(
            $this->createMock(PointInTimeInterface::class),
            new Str(''),
            (new Map('string', Attribute::class))
                ->put('level', new Level('CRITICAL'))
        )));
    }
}
