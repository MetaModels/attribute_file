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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2021 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\EventListener\DcGeneral\Table\Attribute;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\ContainerInterface;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\Attribute\RemoveTypeOptions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * This test the event listener.
 *
 * @covers \MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\Attribute\RemoveTypeOptions
 */
class RemoveTypeOptionsTest extends TestCase
{
    private function mockEnvironment()
    {
        $dataDefinition = null;

        $environment = $this->getMockForAbstractClass(EnvironmentInterface::class);

        $environment
            ->expects(self::any())
            ->method('getDataDefinition')
            ->willReturnCallback(
                function () use (&$dataDefinition) {
                    return $dataDefinition;
                }
            );

        $environment
            ->expects(self::any())
            ->method('setDataDefinition')
            ->willReturnCallback(
                function (ContainerInterface $container) use (&$dataDefinition, $environment) {
                    $dataDefinition = $container;

                    return $environment;
                }
            );

        return $environment;
    }

    private function mockDataDefinition($name = null)
    {
        $dataDefinition = $this->getMockForAbstractClass(ContainerInterface::class);

        $dataDefinition
            ->expects(self::any())
            ->method('getName')
            ->willReturn($name);

        return $dataDefinition;
    }

    private function mockModel()
    {
        return $this->getMockForAbstractClass(ModelInterface::class);
    }

    public function dataProviderTestRemoveOption()
    {
        return [
            [['foo' => 'bar', 'filesort' => 'foo'], 'foo', 'foo', ['foo' => 'bar', 'filesort' => 'foo']],
            [['foo' => 'bar', 'filesort' => 'foo'], 'foo', 'type', ['foo' => 'bar', 'filesort' => 'foo']],
            [['foo' => 'bar'], 'tl_metamodel_attribute', 'type', ['foo' => 'bar']],
            [['foo' => 'bar'], 'tl_metamodel_attribute', 'type', ['foo' => 'bar', 'filesort' => 'foo']]
        ];
    }

    /**
     * @dataProvider dataProviderTestRemoveOption
     */
    public function testRemoveOption(array $expected, $providerName, $propertyName, $options)
    {
        $environment = $this->mockEnvironment();
        $environment->setDataDefinition($this->mockDataDefinition($providerName));

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(
            GetPropertyOptionsEvent::NAME,
            [new RemoveTypeOptions(), 'removeOption']
        );


        $event = new GetPropertyOptionsEvent($environment, $this->mockModel());
        $event->setPropertyName($propertyName);
        $event->setOptions($options);
        $dispatcher->dispatch($event, $event::NAME);

        self::assertSame($expected, $event->getOptions());
    }
}
