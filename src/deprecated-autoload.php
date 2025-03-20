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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use MetaModels\AttributeFileBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\AttributeFileBundle\Attribute\FileOrder;
use MetaModels\AttributeFileBundle\DcGeneral\AttributeFileDefinition;
use MetaModels\AttributeFileBundle\EventListener\ImageSizeOptionsProvider;

// This hack is to load the "old locations" of the classes.
spl_autoload_register(
    function ($class) {
        static $classes = [
            'MetaModels\Attribute\File\File'                    => File::class,
            'MetaModels\Attribute\File\FileOrder'               => FileOrder::class,
            'MetaModels\Attribute\File\AttributeTypeFactory'    => AttributeTypeFactory::class,
            'MetaModels\DcGeneral\AttributeFileDefinition'      => AttributeFileDefinition::class,
            'MetaModels\Events\Attribute\File\ImageSizeOptions' => ImageSizeOptionsProvider::class
        ];

        if (isset($classes[$class])) {
            // @codingStandardsIgnoreStart Silencing errors is discouraged
            @trigger_error('Class "' . $class . '" has been renamed to "' . $classes[$class] . '"', E_USER_DEPRECATED);
            // @codingStandardsIgnoreEnd

            if (!class_exists($classes[$class])) {
                spl_autoload_call($class);
            }

            class_alias($classes[$class], $class);
        }
    }
);
