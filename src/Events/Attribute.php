<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2018 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Events;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Property;
use ContaoCommunityAlliance\DcGeneral\Event\PostPersistModelEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\AttributeFileBundle\DcGeneral\AttributeFileDefinition;
use MetaModels\DcGeneral\Events\MetaModel\BuildAttributeEvent;
use MetaModels\Factory;
use MetaModels\Helper\TableManipulation;
use \ContaoCommunityAlliance\DcGeneral\DataDefinition\ContainerInterface;

/**
 * Class Attribute
 *
 * @package MetaModels\AttributeFileBundle\Events
 */
class Attribute
{
    /**
     * @var Factory|null
     */
    private $factory = null;

    /**
     * Attribute constructor.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * This builds the dc-general property information for the virtual file order attribute.
     *
     * @param BuildAttributeEvent $event The event being processed.
     *
     * @return void
     */
    public function buildAttribute(BuildAttributeEvent $event)
    {
        $attribute = $event->getAttribute();

        if (!($attribute instanceof File) || !$attribute->get('file_multiple')) {
            return;
        }

        $container  = $event->getContainer();
        $properties = $container->getPropertiesDefinition();
        $name       = $attribute->getColName();
        $nameSort   = sprintf('%s__sort', $name);

        if ($properties->hasProperty($nameSort)) {
            $this->addAttributeToDefinition($container, $name);

            $properties->getProperty($name . '__sort')->setWidgetType('fileTreeOrder');

            return;
        }

        $properties->addProperty($property = new DefaultProperty($name . '__sort'));
        $property->setWidgetType('fileTreeOrder');

        $this->addAttributeToDefinition($container, $name);
    }

    /**
     * This handles all file attributes and clones the visible conditions to reflect those of the file attribute.
     *
     * @param BuildDataDefinitionEvent $event The event being processed.
     *
     * @return void
     */
    public function buildDataDefinition(BuildDataDefinitionEvent $event)
    {
        $container = $event->getContainer();
        if (!$container->hasDefinition('metamodels.file-attributes')) {
            return;
        }
        // All properties...
        foreach ($container->getDefinition('metamodels.file-attributes')->get() as $propertyName) {
            // ... in all palettes ...
            foreach ($container->getPalettesDefinition()->getPalettes() as $palette) {
                // ... in any legend ...
                foreach ($palette->getLegends() as $legend) {
                    // ... of the searched name ...
                    if (($legend->hasProperty($propertyName))
                        && ($container->getPropertiesDefinition()->hasProperty($propertyName . '__sort'))
                    ) {
                        // ... must have the order field as companion, visible only when the real property is.
                        $file = $legend->getProperty($propertyName);

                        $legend->addProperty($order = new Property($propertyName . '__sort'), $file);

                        $order->setEditableCondition($file->getEditableCondition());
                        $order->setVisibleCondition($file->getVisibleCondition());
                    }
                }
            }
        }
    }

    /**
     * Handle the update of the file attribute, if switch on for file multiple.
     *
     * @param PostPersistModelEvent $event The event.
     *
     * @return void
     *
     * @throws \Exception If column not exist in the table.
     */
    public function handleUpdateAttribute(PostPersistModelEvent $event)
    {
        $model = $event->getModel();

        if (($model->getProperty('type') !== 'file')
            || (!$model->getProperty('file_multiple'))
            || ($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
        ) {
            return;
        }

        $metaModelsName = $this->factory->translateIdToMetaModelName($model->getProperty('pid'));
        $metaModel      = $this->factory->getMetaModel($metaModelsName);
        $attributeName  = $model->getProperty('colname') . '__sort';

        try {
            TableManipulation::checkColumnExists($metaModel->getTableName(), $attributeName);
        } catch (\Exception $e) {
            TableManipulation::createColumn($metaModel->getTableName(), $attributeName, 'blob NULL');
        }
    }

    /**
     * Add attribute to metamodels file attributes definition.
     *
     * @param ContainerInterface $container The metamodel data definition.
     *
     * @param string             $name      The attribute name.
     *
     * @return void
     */
    protected function addAttributeToDefinition(ContainerInterface $container, $name)
    {
        if (!$container->hasDefinition('metamodels.file-attributes')) {
            $container->setDefinition('metamodels.file-attributes', new AttributeFileDefinition());
        }

        $container->getDefinition('metamodels.file-attributes')->add($name);
    }
}
