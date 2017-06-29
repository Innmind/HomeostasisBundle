<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\DependencyInjection\Compiler;

use Innmind\HomeostasisBundle\DependencyInjection\Compiler\ConfigureFactorsPass;
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};
use PHPUnit\Framework\TestCase;

class ConfigureFactorsPassTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            CompilerPassInterface::class,
            new ConfigureFactorsPass
        );
    }

    public function testProcess()
    {
        $compiler = new ConfigureFactorsPass;
        $container = new ContainerBuilder;
        $container->setParameter(
            'innmind.homeostasis.factors',
            [
                'factor' => 'foo',
                'factor2' => 'bar',
            ]
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
        $this->assertSame('foo', $container->getDefinition('factory')->getArgument(0));
        $this->assertSame('bar', $container->getDefinition('factory2')->getArgument(0));
    }

    /**
     * @expectedException Innmind\HomeostasisBundle\Exception\MissingAliasAttribute
     * @expectedExceptionMessage invalid_factor
     */
    public function testThrowWhenMissingFactorAttribute()
    {
        $compiler = new ConfigureFactorsPass;
        $container = new ContainerBuilder;
        $container->setParameter(
            'innmind.homeostasis.factors',
            [
                'factor' => 'foo',
                'factor2' => 'bar',
            ]
        );
        $container->setDefinition(
            'invalid_factor',
            (new Definition)
                ->addTag('innmind.homeostasis.factor')
        );

        $compiler->process($container);
    }

    /**
     * @expectedException Innmind\HomeostasisBundle\Exception\AliasNameUsedMultipleTimes
     * @expectedExceptionMessage foo
     */
    public function testThrowWhenFactorUsedMultipleTimes()
    {
        $compiler = new ConfigureFactorsPass;
        $container = new ContainerBuilder;
        $container->setParameter(
            'innmind.homeostasis.factors',
            [
                'factor' => 'foo',
                'factor2' => 'bar',
            ]
        );
        $container->setDefinition(
            'factor',
            (new Definition)
                ->addTag('innmind.homeostasis.factor', ['alias' => 'foo'])
        );
        $container->setDefinition(
            'factor2',
            (new Definition)
                ->addTag('innmind.homeostasis.factor', ['alias' => 'foo'])
        );

        $compiler->process($container);
    }
}
