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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Test\Attribute\File;

use MetaModels\Attribute\File\File;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests to test class File.
 */
class FileTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $language         The language.
     * @param string $fallbackLanguage The fallback language.
     *
     * @return \MetaModels\IMetaModel
     */
    protected function mockMetaModel($language, $fallbackLanguage)
    {
        $metaModel = $this->getMockForAbstractClass('MetaModels\IMetaModel');

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('mm_unittest'));

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
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $text = new File($this->mockMetaModel('en', 'en'));
        $this->assertInstanceOf('MetaModels\Attribute\File\File', $text);
    }

    /**
     * Test that empty values are handled correctly.
     *
     * @return void
     */
    public function testEmptyValues()
    {
        $file = new File(
            $this->mockMetaModel('en', 'en'),
            [
                'file_multiple' => false
            ]
        );

        $this->assertEquals(
            ['bin' => [], 'value' => [], 'path' => [], 'meta' => []],
            $file->widgetToValue(null, 1)
        );
        $this->assertEquals(
            ['bin' => [], 'value' => [], 'path' => [], 'meta' => []],
            $file->widgetToValue([], 1)
        );
    }
}
