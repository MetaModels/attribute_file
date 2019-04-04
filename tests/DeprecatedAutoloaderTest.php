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

namespace MetaModels\AttributeFileBundle\Test;

use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\AttributeFileBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeFileBundle\Attribute\FileOrder;
use MetaModels\AttributeFileBundle\DcGeneral\AttributeFileDefinition;
use MetaModels\AttributeFileBundle\EventListener\ImageSizeOptionsListener;
use MetaModels\AttributeFileBundle\Helper\UpgradeHandler;
use PHPUnit\Framework\TestCase;

/**
 * This class tests if the deprecated autoloader works.
 */
class DeprecatedAutoloaderTest extends TestCase
{
    /**
     * Selects of old classes to the new one.
     *
     * @var array
     */
    private static $classes = [
        'MetaModels\Attribute\File\File'                    => File::class,
        'MetaModels\Attribute\File\FileOrder'               => FileOrder::class,
        'MetaModels\Attribute\File\AttributeTypeFactory'    => AttributeTypeFactory::class,
        'MetaModels\Attribute\File\Helper\UpgradeHandler'   => UpgradeHandler::class,
        'MetaModels\DcGeneral\AttributeFileDefinition'      => AttributeFileDefinition::class,
        'MetaModels\Events\Attribute\File\ImageSizeOptions' => ImageSizeOptionsListener::class,
    ];

    /**
     * Provide the alias class map.
     *
     * @return array
     */
    public function provideAliasClassMap()
    {
        $values = [];

        foreach (static::$classes as $select => $class) {
            $values[] = [$select, $class];
        }

        return $values;
    }

    /**
     * Test if the deprecated classes are aliased to the new one.
     *
     * @param string $oldClass Old class name.
     * @param string $newClass New class name.
     *
     * @dataProvider provideAliasClassMap
     */
    public function testDeprecatedClassesAreAliased($oldClass, $newClass)
    {
        $this->assertTrue(\class_exists($oldClass), \sprintf('Class select "%s" is not found.', $oldClass));

        $oldClassReflection = new \ReflectionClass($oldClass);
        $newClassReflection = new \ReflectionClass($newClass);

        $this->assertSame($newClassReflection->getFileName(), $oldClassReflection->getFileName());
    }
}
