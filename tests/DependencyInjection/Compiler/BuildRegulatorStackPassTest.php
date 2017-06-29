<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\DependencyInjection\Compiler;

use Innmind\HomeostasisBundle\DependencyInjection\Compiler\BuildRegulatorStackPass;
use Innmind\Homeostasis\{
    Regulator,
    Regulator\ThreadSafe,
    Strategy
};
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};
use PHPUnit\Framework\TestCase;

class BuildRegulatorStackPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new BuildRegulatorStackPass
        );
    }

    public function testProcess()
    {
        $container = new ContainerBuilder;
        $mock = $this->createMock(Regulator::class);
        $regulator1 = new class($mock, 'foo') implements Regulator {
            public static $called = false;
            private $regulator;

            public function __construct(Regulator $regulator, string $whatever)
            {
                $this->regulator = $regulator;
            }

            public function __invoke(): Strategy
            {
                ($this->regulator)();
                self::$called = true;

                return Strategy::holdSteady();
            }
        };
        $regulator2 = new class('bar', $mock) implements Regulator {
            public static $called = false;
            private $regulator;

            public function __construct(string $whatever, Regulator $regulator)
            {
                $this->regulator = $regulator;
            }

            public function __invoke(): Strategy
            {
                ($this->regulator)();
                self::$called = true;

                return Strategy::holdSteady();
            }
        };
        $container->setDefinition(
            'innmind.homeostasis.thread_safe',
            (new Definition(
                ThreadSafe::class,
                [null]
            ))->addTag(
                'innmind.homeostasis.regulator',
                ['alias' => 'thread_safe']
            )
        );
        $container->setDefinition(
            'innmind.homeostasis.first',
            (new Definition(get_class($regulator1), [null, 'foo']))->addTag(
                'innmind.homeostasis.regulator',
                ['alias' => 'first']
            )
        );
        $container->setDefinition(
            'innmind.homeostasis.second',
            (new Definition(get_class($regulator2), ['bar', null]))->addTag(
                'innmind.homeostasis.regulator',
                ['alias' => 'second']
            )
        );
        $container->setDefinition(
            'innmind.homeostasis.default',
            (new Definition(
                Regulator\Regulator::class,
                [null, null, null, null, null]
            ))->addTag(
                'innmind.homeostasis.regulator',
                ['alias' => 'default']
            )
        );
        $container->setParameter(
            'innmind.homeostasis.regulator_stack',
            ['thread_safe', 'first', 'second', 'default']
        );

        $this->assertNull((new BuildRegulatorStackPass)->process($container));
        $this->assertSame(
            'innmind.homeostasis.thread_safe',
            (string) $container->getAlias('innmind.homeostasis.regulator')
        );
        $this->assertInstanceOf(
            Reference::class,
            $container->getDefinition('innmind.homeostasis.thread_safe')->getArgument(0)
        );
        $this->assertInstanceOf(
            Reference::class,
            $container->getDefinition('innmind.homeostasis.first')->getArgument(0)
        );
        $this->assertInstanceOf(
            Reference::class,
            $container->getDefinition('innmind.homeostasis.second')->getArgument(1)
        );
        $this->assertSame(
            'innmind.homeostasis.first',
            (string) $container->getDefinition('innmind.homeostasis.thread_safe')->getArgument(0)
        );
        $this->assertSame(
            'innmind.homeostasis.second',
            (string) $container->getDefinition('innmind.homeostasis.first')->getArgument(0)
        );
        $this->assertSame(
            'innmind.homeostasis.default',
            (string) $container->getDefinition('innmind.homeostasis.second')->getArgument(1)
        );
    }

    public function testProcessWithOneElementInTheStack()
    {
        $container = new ContainerBuilder;
        $container->setParameter('innmind.homeostasis.regulator_stack', ['default']);
        $container->setDefinition(
            'innmind.homeostasis.default',
            (new Definition(
                Regulator\Regulator::class,
                [null, null, null, null, null]
            ))->addTag(
                'innmind.homeostasis.regulator',
                ['alias' => 'default']
            )
        );

        $this->assertNull((new BuildRegulatorStackPass)->process($container));
        $this->assertSame(
            'innmind.homeostasis.default',
            (string) $container->getAlias('innmind.homeostasis.regulator')
        );
    }

    /**
     * @expectedException Innmind\HomeostasisBundle\Exception\MissingAliasAttribute
     */
    public function testThrowWhenMissingAlias()
    {
        $container = new ContainerBuilder;
        $container->setParameter('innmind.homeostasis.regulator_stack', ['default']);
        $container->setDefinition(
            'command_bus.default',
            (new Definition(
                Regulator\Regulator::class,
                [null, null, null, null, null]
            ))
                ->addTag('innmind.homeostasis.regulator')
        );

        (new BuildRegulatorStackPass)->process($container);
    }

    /**
     * @expectedException Innmind\HomeostasisBundle\Exception\MissingRegulatorArgument
     * @expectedExceptionMessageRegExp /^class@anonymous.+$/
     */
    public function testThrowWhenNoRegulatorTypeHint()
    {
        $container = new ContainerBuilder;
        $regulator1 = new class implements Regulator {
            public function __construct()
            {
            }

            public function __invoke(): Strategy
            {
            }
        };
        $container->setDefinition(
            'command_bus.thread_safe',
            (new Definition(ThreadSafe::class, [null]))->addTag(
                'innmind.homeostasis.regulator',
                ['alias' => 'thread_safe']
            )
        );
        $container->setDefinition(
            'command_bus.first',
            (new Definition(get_class($regulator1)))->addTag(
                'innmind.homeostasis.regulator',
                ['alias' => 'first']
            )
        );
        $container->setDefinition(
            'command_bus.default',
            (new Definition(
                Regulator\Regulator::class,
                [null, null, null, null, null]
            ))
                ->addTag('innmind.homeostasis.regulator', ['alias' => 'default'])
        );
        $container->setParameter(
            'innmind.homeostasis.regulator_stack',
            ['thread_safe', 'first', 'default']
        );

        (new BuildRegulatorStackPass)->process($container);
    }
}
