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
 * @subpackage Tests
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Test\Attribute\File;

use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Attribute\File\AttributeTypeFactory;
use MetaModels\IMetaModel;
use MetaModels\Test\Attribute\AttributeTypeFactoryTest;
use MetaModels\Attribute\File\File;

/**
 * Test the attribute factory.
 */
class FileAttributeTypeFactoryTest extends AttributeTypeFactoryTest
{
    /**
     * Mock a MetaModel.
     *
     * @param string $tableName        The table name.
     *
     * @param string $language         The language.
     *
     * @param string $fallbackLanguage The fallback language.
     *
     * @return IMetaModel
     */
    protected function mockMetaModel($tableName, $language, $fallbackLanguage)
    {
        $metaModel = $this->getMockForAbstractClass(IMetaModel::class);

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));

        $metaModel
            ->expects($this->any())
            ->method('getActiveLanguage')
            ->will($this->returnValue($language));

        $metaModel
            ->expects($this->any())
            ->method('getFallbackLanguage')
            ->will($this->returnValue($fallbackLanguage));

        return $metaModel;
    }

    /**
     * Override the method to run the tests on the attribute factories to be tested.
     *
     * @return IAttributeTypeFactory[]
     */
    protected function getAttributeFactories()
    {
        return [new AttributeTypeFactory()];
    }

    /**
     * Test creation of a file attribute.
     *
     * @return void
     */
    public function testCreateSelect()
    {
        $factory   = new AttributeTypeFactory();
        $values    = [];
        $attribute = $factory->createInstance(
            $values,
            $this->mockMetaModel('mm_test', 'de', 'en')
        );

        $this->assertInstanceOf(File::class, $attribute);

        foreach ($values as $key => $value) {
            $this->assertEquals($value, $attribute->get($key), $key);
        }
    }
}
