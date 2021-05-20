<?php

namespace Eikona\Tessa\ConnectorBundle;

use Eikona\Tessa\ConnectorBundle\DependencyInjection\Compiler\OroConfigCompilerPass;
use Eikona\Tessa\ConnectorBundle\DependencyInjection\EikonaTessaConnectorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EikonaTessaConnectorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new OroConfigCompilerPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new EikonaTessaConnectorExtension();
        }

        return $this->extension;
    }
}
