<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\DependencyInjection;

use Symfony\Component\Config\Definition\{
    Builder\TreeBuilder,
    ConfigurationInterface
};

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $root = $treeBuilder->root('innmind_homeostasis');

        $root
            ->children()
                ->arrayNode('factors')
                    ->requiresAtLeastOneElement()
                    ->defaultValue([
                        'cpu' => [
                            'weight' => 0.7,
                            'polynom' => [
                                'intercept' => -0.0012195890835040666,
                                'degrees' => [
                                    1 => 2.0996410102652,
                                    2 => -17.27684076838,
                                    3 => 86.261146237871,
                                    4 => -189.7736029403,
                                    5 => 184.66906744449,
                                    6 => -64.975065630889,
                                ],
                            ],
                        ],
                        'log' => [
                            'weight' => 0.3,
                            'polynom' => [
                                'intercept' => -5.03E-8,
                                'degrees' => [
                                    1 => 12.4035,
                                    2 => -52.3392,
                                    3 => 87.7193,
                                    4 => -46.7836,
                                ],
                            ]
                        ],
                    ])
                    ->prototype('array')->end()
                ->end()
                ->arrayNode('regulator_stack')
                    ->requiresAtLeastOneElement()
                    ->defaultValue(['thread_safe', 'modulate_state_history', 'default'])
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('strategy_determinator')
                    ->defaultValue('innmind.homeostasis.strategy_determinator.default')
                ->end()
                ->scalarNode('actuator')
                    ->info('Service id of the actuator')
                ->end()
                ->scalarNode('state_history')
                    ->defaultValue('%kernel.root_dir%/../var/data/innmind/homeostasis/states')
                ->end()
                ->scalarNode('action_history')
                    ->defaultValue('%kernel.root_dir%/../var/data/innmind/homeostasis/actions')
                ->end()
                ->scalarNode('max_history')
                    ->defaultValue(24 * 60 * 60 * 1000) //one day
                ->end()
                ->scalarNode('min_history')
                    ->defaultValue(60 * 60 * 1000) //one hour
                ->end()
            ->end();

        return $treeBuilder;
    }
}
