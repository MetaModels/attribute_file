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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\EventListener;

use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Property;
use MetaModels\AttributeFileBundle\DcGeneral\AttributeFileDefinition;

/**
 * Class BuildDataDefinitionListener
 */
class BuildDataDefinitionListener
{
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
        $definition = $container->getDefinition('metamodels.file-attributes');
        assert($definition instanceof AttributeFileDefinition);
        foreach ($definition->get() as $propertyName) {
            // ... in all palettes ...
            foreach ($container->getPalettesDefinition()->getPalettes() as $palette) {
                // ... in any legend ...
                foreach ($palette->getLegends() as $legend) {
                    // ... of the searched name ...
                    if (
                        ($legend->hasProperty($propertyName))
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
}
