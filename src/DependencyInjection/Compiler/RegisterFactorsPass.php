<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface,
    Reference
};

final class RegisterFactorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds('innmind.homeostasis.factor');
        $factors = [];

        foreach ($services as $id => $tags) {
            $factors[] = new Reference($id);
        }

        $container
            ->getDefinition('innmind.homeostasis.regulator.default')
            ->replaceArgument(0, $factors);
    }
}
