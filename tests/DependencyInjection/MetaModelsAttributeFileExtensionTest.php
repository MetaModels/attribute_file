<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2021 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2021 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\DependencyInjection;

use MetaModels\AttributeFileBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeFileBundle\DependencyInjection\MetaModelsAttributeFileExtension;
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
    /**
     * Test that extension can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $extension = new MetaModelsAttributeFileExtension();

        self::assertInstanceOf(MetaModelsAttributeFileExtension::class, $extension);
        self::assertInstanceOf(ExtensionInterface::class, $extension);
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     */
    public function testFactoryIsRegistered()
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();

        $container
            ->expects(self::atLeastOnce())
            ->method('setDefinition')
            ->withConsecutive(
                [
                    'metamodels.attribute_file.factory',
                    self::callback(
                        function ($value) {
                            /** @var Definition $value */
                            $this->assertInstanceOf(Definition::class, $value);
                            $this->assertEquals(AttributeTypeFactory::class, $value->getClass());
                            $this->assertCount(1, $value->getTag('metamodels.attribute_factory'));

                            return true;
                        }
                    )
                ],
                [
                    'metamodels.attribute_file.event_listener.image_size_options',
                    $this->callback(function ($value) {
                        /** @var Definition $value */
                        $this->assertInstanceOf(Definition::class, $value);
                        $this->assertEquals(ImageSizeOptionsListener::class, $value->getClass());
                        $this->assertCount(1, $value->getTag('kernel.event_listener'));

                        return true;
                    })
                ],
                [
                    'metamodels.attribute_file.event_listener.build_attribute',
                    $this->callback(function ($value) {
                        /** @var Definition $value */
                        $this->assertInstanceOf(Definition::class, $value);
                        $this->assertEquals(BuildAttributeListener::class, $value->getClass());
                        $this->assertCount(1, $value->getTag('kernel.event_listener'));

                        return true;
                    })
                ],
                [
                    'metamodels.attribute_file.event_listener.build-data-definition',
                    $this->callback(function ($value) {
                        /** @var Definition $value */
                        $this->assertInstanceOf(Definition::class, $value);
                        $this->assertEquals(BuildDataDefinitionListener::class, $value->getClass());
                        $this->assertCount(1, $value->getTag('kernel.event_listener'));

                        return true;
                    })
                ],
                [
                    DoctrineSchemaGenerator::class,
                    $this->callback(
                        function ($value) {
                            /** @var Definition $value */
                            $this->assertInstanceOf(Definition::class, $value);
                            $this->assertCount(1, $value->getTag('metamodels.schema-generator.doctrine'));

                            return true;
                        }
                    )
                ]
            );
        $container
            ->method('getParameter')
            ->willReturn(false, 'cache/dir', [MetaModelsContaoFrontendEditingBundle::class]);

        $definition = $this->createMock(Definition::class);

        $definition
            ->method('setArgument')
            ->willReturnCallback(
                function (string $key, bool $value) {
                    switch ($key) {
                        case '$frontendEditing':
                            self::assertTrue($value);
                            break;
                        default:
                    }
                }
            );

        $container
            ->method('getDefinition')
            ->willReturn($definition);

        $extension = new MetaModelsAttributeFileExtension();
        $extension->load([], $container);

    }
}
