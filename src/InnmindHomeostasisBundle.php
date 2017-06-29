<?php
declare(strict_types = 1);

namespace Innmind\HomeostasisBundle;

use Innmind\HomeostasisBundle\DependencyInjection\Compiler\{
    ConfigureFactorsPass,
    BuildRegulatorStackPass
};
use Symfony\Component\{
    HttpKernel\Bundle\Bundle,
    DependencyInjection\ContainerBuilder
};

final class InnmindHomeostasisBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new ConfigureFactorsPass)
            ->addCompilerPass(new BuildRegulatorStackPass);
    }
}
