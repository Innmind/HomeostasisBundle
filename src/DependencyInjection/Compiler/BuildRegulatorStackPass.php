<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\DependencyInjection\Compiler;

use Innmind\HomeostasisBundle\Exception\{
    MissingAliasAttribute,
    MissingRegulatorArgument
};
use Innmind\Homeostasis\Regulator;
use Symfony\Component\DependencyInjection\{
    Compiler\CompilerPassInterface,
    ContainerBuilder,
    Definition,
    Reference
};

final class BuildRegulatorStackPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $stack = $container->getParameter('innmind.homeostasis.regulator_stack');
        $services = $this->searchServices($container);

        $container->setAlias(
            'innmind.homeostasis.regulator',
            $services[$stack[0]]
        );

        if (count($stack) === 1) {
            return;
        }

        for ($i = 0, $count = count($stack) - 1; $i < $count; $i++) {
            $alias = $stack[$i];
            $next = $stack[$i + 1];

            $this->inject(
                $container->getDefinition($services[$alias]),
                $services[$next]
            );
        }
    }

    private function searchServices(ContainerBuilder $container): array
    {
        $services = $container->findTaggedServiceIds('innmind.homeostasis.regulator');
        $map = [];

        foreach ($services as $id => $tags) {
            foreach ($tags as $tag => $attributes) {
                if (!isset($attributes['alias'])) {
                    throw new MissingAliasAttribute($id);
                }

                $map[$attributes['alias']] = $id;
            }
        }

        return $map;
    }

    private function inject(
        Definition $definition,
        string $next
    ) {
        $class = $definition->getClass();
        $refl = new \ReflectionMethod($class, '__construct');

        foreach ($refl->getParameters() as $parameter) {
            if ((string) $parameter->getType() !== Regulator::class) {
                continue;
            }

            $definition->replaceArgument(
                $parameter->getPosition(),
                new Reference($next)
            );

            return;
        }

        throw new MissingRegulatorArgument($class);
    }
}
