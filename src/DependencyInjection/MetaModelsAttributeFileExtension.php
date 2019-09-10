<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\DependencyInjection;

use Doctrine\Common\Cache\ArrayCache;
use MetaModels\ContaoFrontendEditingBundle\MetaModelsContaoFrontendEditingBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages the bundle configuration
 */
class MetaModelsAttributeFileExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('factory.yml');
        $loader->load('event_listener.yml');
        $loader->load('services.yml');

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);
        $this->buildCacheService($container, $config);

        // Load configuration for the frontend editing.
        if (\in_array(MetaModelsContaoFrontendEditingBundle::class, $container->getParameter('kernel.bundles'), true)) {
            $loader->load('frontend_editing/event_listener.yml');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration(
            $container->getParameter('kernel.debug'),
            $container->getParameter('metamodels.cache_dir')
        );
    }

    /**
     * Build the cache service.
     *
     * @param ContainerBuilder $container The container builder.
     * @param array            $config    The configuration.
     *
     * @return void
     */
    private function buildCacheService(ContainerBuilder $container, array $config)
    {
        // if cache disabled, swap it out with the dummy cache.
        if (!$config['enable_cache']) {
            $cache = $container->getDefinition('metamodels.attribute_file.cache');
            $cache->setClass(ArrayCache::class);
            $cache->setArguments([]);
            $container->setParameter('metamodels.attribute_file.cache_dir', null);
            return;
        }

        $container->setParameter('metamodels.attribute_file.cache_dir', $config['cache_dir']);
    }
}
