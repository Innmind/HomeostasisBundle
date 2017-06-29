<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle\DependencyInjection;

use Symfony\Component\{
    HttpKernel\DependencyInjection\Extension,
    DependencyInjection\ContainerBuilder,
    DependencyInjection\Loader,
    DependencyInjection\Reference,
    Config\FileLocator
};

final class InnmindHomeostasisExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
        $config = $this->processConfiguration(
            new Configuration,
            $configs
        );

        $container->setParameter(
            'innmind.homeostasis.factors',
            $config['factors']
        );
        $container->setParameter(
            'innmind.homeostasis.regulator_stack',
            $config['regulator_stack']
        );
        $container->setAlias(
            'innmind.homeostasis.strategy_determinator',
            $config['strategy_determinator']
        );
        $container
            ->getDefinition('innmind.homeostasis.regulator.default')
            ->replaceArgument(
                4,
                new Reference($config['actuator'])
            );
        $container
            ->getDefinition('innmind.homeostasis.state_history.filesystem')
            ->replaceArgument(
                0,
                $config['state_history']
            );
        $container
            ->getDefinition('innmind.homeostasis.action_history.filesystem')
            ->replaceArgument(
                0,
                $config['action_history']
            );
        $container
            ->getDefinition('innmind.homeostasis.regulator.modulate_state_history.max_history')
            ->replaceArgument(
                0,
                $config['max_history']
            );
        $container
            ->getDefinition('innmind.homeostasis.regulator.modulate_state_history.min_history')
            ->replaceArgument(
                0,
                $config['min_history']
            );
    }
}
