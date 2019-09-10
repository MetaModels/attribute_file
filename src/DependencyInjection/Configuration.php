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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeFileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Adds the Contao configuration structure.
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * The debug flag.
     *
     * @var bool
     */
    private $debug;

    /**
     * The root directory.
     *
     * @var string
     */
    private $rootDir;

    /**
     * Constructor.
     *
     * @param bool        $debug   The debug flag.
     * @param string|null $rootDir The root directory.
     */
    public function __construct(bool $debug, ?string $rootDir)
    {
        $this->debug   = $debug;
        $this->rootDir = $rootDir;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('metamodels_attribute_file');

        $rootNode
            ->children()
                ->booleanNode('enable_cache')
                    ->defaultValue(!$this->debug)
                ->end()
                ->scalarNode('cache_dir')
                    ->defaultValue('%metamodels.cache_dir%' . DIRECTORY_SEPARATOR . 'attribute_file')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
