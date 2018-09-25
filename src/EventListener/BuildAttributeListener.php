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

namespace MetaModels\AttributeFileBundle\EventListener;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\Properties\DefaultProperty;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\AttributeFileBundle\DcGeneral\AttributeFileDefinition;
use MetaModels\DcGeneral\Events\MetaModel\BuildAttributeEvent;
use \ContaoCommunityAlliance\DcGeneral\DataDefinition\ContainerInterface;

/**
 * Class Attribute
 *
 * @package MetaModels\AttributeFileBundle\Events
 */
class BuildAttributeListener
{
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
     * Add attribute to MetaModels file attributes definition.
     *
     * @param ContainerInterface $container The metamodel data definition.
     *
     * @param string             $name      The attribute name.
     *
     * @return void
     */
    private function addAttributeToDefinition(ContainerInterface $container, $name)
    {
        if (!$container->hasDefinition('metamodels.file-attributes')) {
            $container->setDefinition('metamodels.file-attributes', new AttributeFileDefinition());
        }

        $container->getDefinition('metamodels.file-attributes')->add($name);
    }
}
