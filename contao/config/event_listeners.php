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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use MetaModels\Attribute\File\AttributeTypeFactory;
use MetaModels\Attribute\Events\CreateAttributeFactoryEvent;
use MetaModels\MetaModelsEvents;

return array
(
    MetaModelsEvents::ATTRIBUTE_FACTORY_CREATE => array(
        function (CreateAttributeFactoryEvent $event) {
            $factory = $event->getFactory();
            $factory->addTypeFactory(new AttributeTypeFactory());
        }
    ),
    GetPropertyOptionsEvent::NAME => array(
        function (GetPropertyOptionsEvent $event) {
            if (('tl_metamodel_dcasetting' !== $event->getEnvironment()->getDataDefinition()->getName())
                || ('fe_widget' !== $event->getPropertyName())) {
                return;
            }

            $options = [];
            foreach ($GLOBALS['TL_FFL'] as $ffl => $fflClass) {
                if (in_array(
                    'MetaModels\Attribute\File\Contao\Widget\IFileWidget',
                    class_implements($fflClass, true)
                )) {
                    $options[] = $ffl;
                }
            }

            $event->setOptions($options);
        }
    )
);
