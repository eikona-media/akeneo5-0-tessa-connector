<?php
/**
 * EikonaTessaReferenceDataAttributeExtension.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ReferenceDataAttributeBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EikonaTessaReferenceDataAttributeExtension extends Extension
{

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('attribute.yml');
        $loader->load('event_listener.yml');
        $loader->load('json_schema_validators.yml');
        $loader->load('notification_normalizers.yml');
        $loader->load('persistence.yml');
    }
}
