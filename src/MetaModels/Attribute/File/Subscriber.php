<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2015 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\File;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyEditableCondition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyVisibleCondition;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Property;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use MetaModels\DcGeneral\AttributeFileDefinition;
use MetaModels\DcGeneral\Events\BaseSubscriber;
use MetaModels\DcGeneral\Events\MetaModel\BuildAttributeEvent;

/**
 * Subscriber integrates file attribute related listeners.
 *
 * @package MetaModels\Attribute\File
 */
class Subscriber extends BaseSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function registerEventsInDispatcher()
    {
        $this
            ->addListener(
                BuildAttributeEvent::NAME,
                array($this, 'buildAttribute')
            )
            ->addListener(
                BuildDataDefinitionEvent::NAME,
                array($this, 'buildDataDefinition'),
                // Ensure to be after MetaModels\DcGeneral\Dca\Builder\Builder::PRIORITY (currently 50).
                0
            );
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

        if (!($attribute instanceof File)) {
            return;
        }

        $container  = $event->getContainer();
        $properties = $container->getPropertiesDefinition();
        $name       = $attribute->getColName();

        if ($properties->hasProperty($name . '_sort')) {
            return;
        }

        $properties->addProperty($property = new DefaultProperty($name . '_sort'));
        $property->setWidgetType('fileTreeOrder');

        if (!$container->hasDefinition('metamodels.file-attributes')) {
            $container->setDefinition('metamodels.file-attributes', new AttributeFileDefinition());
        }

        $container->getDefinition('metamodels.file-attributes')->add($name);
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
                    if ($legend->hasProperty($propertyName)) {
                        // ... must have the order field as companion, visible only when the real property is.
                        $file = $legend->getProperty($propertyName);

                        $legend->addProperty($order = new Property($propertyName . '_sort'), $file);

                        $order->setEditableCondition(new PropertyEditableCondition($file->getName()));
                        $order->setVisibleCondition(new PropertyVisibleCondition($file->getName()));
                    }
                }
            }
        }
    }
}
