<?php
declare(strict_types = 1);

namespace Tests\Innmind\HomeostasisBundle\DependencyInjection;

use Innmind\HomeostasisBundle\{
    DependencyInjection\InnmindHomeostasisExtension,
    InnmindHomeostasisBundle
};
use Innmind\Homeostasis\{
    Regulator\ThreadSafe,
    Actuator
};
use Symfony\Component\{
    DependencyInjection\ContainerBuilder,
    HttpKernel\DependencyInjection\Extension,
    Filesystem\Filesystem
};
use PHPUnit\Framework\TestCase;

class InnmindHomeostasisExtensionTest extends TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder;
        $extension = new InnmindHomeostasisExtension;

        $container->set(
            'actuator_service',
            $this->createMock(Actuator::class)
        );
        (new Filesystem)->mkdir(getcwd().'/tmp/app');
        $container->setParameter('kernel.root_dir', getcwd().'/tmp/app');
        $container->setParameter('kernel.logs_dir', getcwd().'/tmp/var/logs');

        $this->assertInstanceOf(Extension::class, $extension);
        $this->assertNull($extension->load(
            [[
                'actuator' => 'actuator_service',
            ]],
            $container
        ));

        (new InnmindHomeostasisBundle)->build($container);
        $container->compile();

        $this->assertTrue($container->hasParameter('innmind.homeostasis.regulator_stack'));
        $this->assertTrue($container->hasParameter('innmind.homeostasis.factors'));
        $this->assertSame(
            ['thread_safe', 'modulate_state_history', 'default'],
            $container->getParameter('innmind.homeostasis.regulator_stack')
        );
        $this->assertSame(
            ['cpu', 'log'],
            array_keys($container->getParameter('innmind.homeostasis.factors'))
        );
        $this->assertInstanceOf(
            ThreadSafe::class,
            $container->get('innmind.homeostasis.regulator')
        );
        (new Filesystem)->remove(getcwd().'/tmp');
    }
}
