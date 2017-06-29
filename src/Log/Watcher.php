<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\Log;

use Innmind\LogReader\Log;

final class Watcher
{
    private $levels;

    public function __construct(string ...$levels)
    {
        $this->levels = $levels;
    }

    public function __invoke(Log $line): bool
    {
        return $line->attributes()->contains('level') && in_array(
            $line->attributes()->get('level')->value(),
            $this->levels,
            true
        );
    }
}
