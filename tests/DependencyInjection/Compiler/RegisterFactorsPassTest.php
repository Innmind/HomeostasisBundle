<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\DependencyInjection\Compiler;

use Innmind\HomeostasisBundle\DependencyInjection\Compiler\RegisterFactorsPass;
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};
use PHPUnit\Framework\TestCase;

class RegisterFactorsPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new RegisterFactorsPass
        );
    }

    public function testProcess()
    {
        $compiler = new RegisterFactorsPass;
        $container = new ContainerBuilder;
        $container->setDefinition(
            'innmind.homeostasis.regulator.default',
            new Definition(
                'regulator',
                [[]]
            )
        );
        $container->setDefinition('factory', new Definition);
        $container->setDefinition('factory2', new Definition);
        $container->setDefinition(
            'factor',
            (new Definition)
                ->setFactory([new Reference('factory'), 'make'])
                ->addTag('innmind.homeostasis.factor', ['alias' => 'factor'])
        );
        $container->setDefinition(
            'factor2',
            (new Definition)
                ->setFactory([new Reference('factory2'), 'make'])
                ->addTag('innmind.homeostasis.factor', ['alias' => 'factor2'])
        );
        $container->setDefinition(
            'factor3',
            (new Definition)
                ->setFactory(['static', 'make'])
                ->addTag('innmind.homeostasis.factor', ['alias' => 'factor3'])
        );

        $this->assertNull($compiler->process($container));
        $factors = $container
            ->getDefinition('innmind.homeostasis.regulator.default')
            ->getArgument(0);
        $this->assertCount(3, $factors);
        $this->assertInstanceOf(Reference::class, $factors[0]);
        $this->assertInstanceOf(Reference::class, $factors[1]);
        $this->assertInstanceOf(Reference::class, $factors[2]);
        $this->assertSame('factor', (string) $factors[0]);
        $this->assertSame('factor2', (string) $factors[1]);
        $this->assertSame('factor3', (string) $factors[2]);
    }
}
