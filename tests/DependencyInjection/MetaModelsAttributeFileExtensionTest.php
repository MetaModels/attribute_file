<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\DependencyInjection;

use MetaModels\AttributeFileBundle\DependencyInjection\MetaModelsAttributeFileExtension;
use MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\DcaSetting\FileWidgetModeOptions;
use MetaModels\ContaoFrontendEditingBundle\MetaModelsContaoFrontendEditingBundle;
use MetaModels\AttributeFileBundle\EventListener\BuildAttributeListener;
use MetaModels\AttributeFileBundle\EventListener\BuildDataDefinitionListener;
use MetaModels\AttributeFileBundle\EventListener\ImageSizeOptionsListener;
use MetaModels\AttributeFileBundle\Schema\DoctrineSchemaGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 *
 * @covers \MetaModels\AttributeFileBundle\DependencyInjection\MetaModelsAttributeFileExtension
 */
class MetaModelsAttributeFileExtensionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $extension = new MetaModelsAttributeFileExtension();

        self::assertInstanceOf(MetaModelsAttributeFileExtension::class, $extension);
        self::assertInstanceOf(ExtensionInterface::class, $extension);
    }

    public function testFactoryIsRegistered(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);
        $container->setParameter('metamodels.cache_dir', 'cache/dir');
        $container->setParameter('kernel.bundles', [MetaModelsContaoFrontendEditingBundle::class]);

        $extension = new MetaModelsAttributeFileExtension();
        $extension->load([], $container);

        self::assertTrue($container->hasAlias('metamodels.attribute_file.toolbox.file'));

        self::assertTrue($container->hasDefinition('metamodels.attribute_file.event_listener.build_attribute'));
        $definition = $container->getDefinition('metamodels.attribute_file.event_listener.build_attribute');
        self::assertCount(1, $definition->getTag('kernel.event_listener'));

        self::assertTrue($container->hasDefinition('metamodels.attribute_file.event_listener.image_size_options'));
        $definition = $container->getDefinition('metamodels.attribute_file.event_listener.image_size_options');
        self::assertCount(1, $definition->getTag('kernel.event_listener'));

        self::assertTrue($container->hasDefinition('metamodels.attribute_file.event_listener.build-data-definition'));
        $definition = $container->getDefinition('metamodels.attribute_file.event_listener.build-data-definition');
        self::assertCount(1, $definition->getTag('kernel.event_listener'));

        self::assertTrue($container->hasDefinition(DoctrineSchemaGenerator::class));
        $definition = $container->getDefinition(DoctrineSchemaGenerator::class);
        self::assertCount(1, $definition->getTag('metamodels.schema-generator.doctrine'));

        self::assertTrue($container->hasParameter('metamodels.managed-schema-type-names'));
        self::assertSame(['file'], $container->getParameter('metamodels.managed-schema-type-names'));
        self::assertTrue($container->hasParameter('metamodels.attribute_file.cache_dir'));
        self::assertSame(
            '%metamodels.cache_dir%/attribute_file',
            $container->getParameter('metamodels.attribute_file.cache_dir')
        );

        self::assertTrue($container->hasDefinition(FileWidgetModeOptions::class));
        $definition = $container->getDefinition(FileWidgetModeOptions::class);
        self::assertTrue($definition->getArgument('$frontendEditing'));
    }
}
