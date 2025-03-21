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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2025 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeFileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
    private bool $debug;

    /**
     * Constructor.
     *
     * @param bool $debug The debug flag.
     */
    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('metamodels_attribute_file');

        $rootNode = $treeBuilder->getRootNode();
        assert($rootNode instanceof ArrayNodeDefinition);
        $children = $rootNode->children();
        $children->booleanNode('enable_cache')->defaultValue(!$this->debug)->end();
        $children
            ->scalarNode('cache_dir')
                ->defaultValue('%metamodels.cache_dir%' . DIRECTORY_SEPARATOR . 'attribute_file')
            ->end();
        $children->booleanNode('file_usage')->defaultValue(false)->end();

        return $treeBuilder;
    }
}
