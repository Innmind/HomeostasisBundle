<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\DependencyInjection\Compiler;

use Innmind\HomeostasisBundle\Exception\{
    MissingAliasAttribute,
    AliasNameUsedMultipleTimes
};
use Innmind\Immutable\Map;
use Symfony\Component\DependencyInjection\{
    ContainerBuilder,
    Compiler\CompilerPassInterface,
    Definition,
    Reference
};

final class ConfigureFactorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('innmind.homeostasis.factors');
        $factors = $container->findTaggedServiceIds('innmind.homeostasis.factor');
        $map = new Map('string', Definition::class);

        foreach ($factors as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    throw new MissingAliasAttribute($id);
                }

                $alias = $attributes['alias'];

                if ($map->contains($alias)) {
                    throw new AliasNameUsedMultipleTimes($alias);
                }

                $map = $map->put($alias, $container->getDefinition($id));
            }
        }

        $map
            ->filter(static function(string $alias, Definition $definition) use ($config): bool {
                return isset($config[$alias]) &&
                    is_array($definition->getFactory()) &&
                    $definition->getFactory()[0] instanceof Reference;
            })
            ->foreach(static function(string $alias, Definition $definition) use ($config, $container): void {
                $container
                    ->getDefinition((string) $definition->getFactory()[0])
                    ->addArgument($config[$alias]);
            });
    }
}
