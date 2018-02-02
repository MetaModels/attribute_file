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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\Attribute;

use Doctrine\DBAL\Connection;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\Helper\TableManipulator;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests to test class File.
 */
class FileTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $tableName        The table name.
     *
     * @param string $language         The language.
     *
     * @return IMetaModel
     */
    protected function mockMetaModel($tableName, $language)
    {
        $metaModel = $this->getMockForAbstractClass('MetaModels\IMetaModel');

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));

        $metaModel
            ->expects($this->any())
            ->method('getActiveLanguage')
            ->will($this->returnValue($language));

        return $metaModel;
    }

    /**
     * Mock the database connection.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    private function mockConnection()
    {
        return $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Mock the table manipulator.
     *
     * @param Connection $connection The database connection mock.
     *
     * @return TableManipulator|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockTableManipulator(Connection $connection)
    {
        return $this->getMockBuilder(TableManipulator::class)
            ->setConstructorArgs([$connection, []])
            ->getMock();
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $connection  = $this->mockConnection();
        $manipulator = $this->mockTableManipulator($connection);

        $text = new File($this->mockMetaModel('en', 'en'), [], $connection, $manipulator);
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
            ['file_multiple' => false],
            $connection = $this->mockConnection(),
            $this->mockTableManipulator($connection)
        );

        $this->assertEquals(
            ['bin' => [], 'value' => [], 'path' => [], 'meta' => []],
            $file->widgetToValue(null, 1)
        );
        $this->assertEquals(
            ['bin' => [], 'value' => [], 'path' => [], 'meta' => []],
            $file->widgetToValue(array(), 1)
        );
    }
}
