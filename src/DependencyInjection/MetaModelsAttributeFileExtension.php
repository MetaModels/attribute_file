<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2025 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2025 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\DependencyInjection;

use InspiredMinds\ContaoFileUsage\ContaoFileUsageBundle;
use MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\DcaSetting\FileWidgetModeOptions;
use MetaModels\ContaoFrontendEditingBundle\MetaModelsContaoFrontendEditingBundle;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

use function assert;
use function in_array;
use function is_array;
use function is_bool;

/**
 * This is the class that loads and manages the bundle configuration
 */
class MetaModelsAttributeFileExtension extends Extension
{
    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('factory.yml');
        $loader->load('event_listener.yml');
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        assert($configuration instanceof Configuration);
        $config = $this->processConfiguration($configuration, $configs);
        $this->buildCacheService($container, $config);

        $bundles = $container->getParameter('kernel.bundles');
        assert(is_array($bundles));

        // Load configuration for the frontend editing extension.
        $frontendEditing = false;
        if (in_array(MetaModelsContaoFrontendEditingBundle::class, $bundles, true)) {
            $frontendEditing = true;
            $loader->load('frontend_editing/event_listener.yml');
        }
        $this->addFrontendEditingArgument($container, $frontendEditing);

        // Load configuration for the file usage extension.
        if (in_array(ContaoFileUsageBundle::class, $bundles, true) && (bool) ($config['file_usage'] ?? false)) {
            $loader->load('file_usage/services.yml');
        }

        // Schema manager
        $typeNames                = $container->hasParameter('metamodels.managed-schema-type-names')
            ? $container->getParameter('metamodels.managed-schema-type-names')
            : null;
        $managedSchemaTypeNames   = is_array($typeNames) ? $typeNames : [];
        $managedSchemaTypeNames[] = 'file';
        $container->setParameter('metamodels.managed-schema-type-names', $managedSchemaTypeNames);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        $debug = $container->getParameter('kernel.debug');
        assert(is_bool($debug));

        return new Configuration($debug);
    }

    /**
     * Build the cache service.
     *
     * @param ContainerBuilder $container The container builder.
     * @param array            $config    The configuration.
     *
     * @return void
     */
    private function buildCacheService(ContainerBuilder $container, array $config): void
    {
        // If cache disabled, swap it out with the dummy cache.
        if (!$config['enable_cache']) {
            $cache = $container->getDefinition('metamodels.attribute_file.cache_system');
            $cache->setClass(ArrayAdapter::class);
            $cache->setArguments([]);
            $container->setParameter('metamodels.attribute_file.cache_dir', null);
            return;
        }

        $container->setParameter('metamodels.attribute_file.cache_dir', $config['cache_dir']);
    }

    /**
     * Add the frontend editing argument to service, who it used.
     *
     * @param ContainerBuilder $container       The container builder.
     * @param bool             $frontendEditing Is frontend editing extension installed.
     *
     * @return void
     */
    private function addFrontendEditingArgument(ContainerBuilder $container, bool $frontendEditing): void
    {
        $container->getDefinition(FileWidgetModeOptions::class)->setArgument('$frontendEditing', $frontendEditing);
    }
}
