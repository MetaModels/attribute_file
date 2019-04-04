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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\Attribute;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Image\ImageFactoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\AttributeFileBundle\Attribute\AttributeOrderTypeFactory;
use MetaModels\AttributeFileBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\AttributeFileBundle\Attribute\FileOrder;
use MetaModels\Helper\TableManipulator;
use MetaModels\Helper\ToolboxFile;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Test the attribute factory.
 *
 * @covers \MetaModels\AttributeFileBundle\Attribute\AttributeTypeFactory
 * @covers \MetaModels\AttributeFileBundle\Attribute\AttributeOrderTypeFactory
 */
class FileAttributeTypeFactoryTest extends TestCase
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
     * Mock the database connection.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    private function mockConnection(AbstractSchemaManager $schemaManager = null)
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection
            ->expects($this->any())
            ->method('getSchemaManager')
            ->willReturn($schemaManager);

        return $connection;
    }

    private function mockSchemaManager(array $tableSchema = [])
    {
        $manager = $this->getMockForAbstractClass(
            AbstractSchemaManager::class,
            [],
            '',
            false,
            true,
            true,
            ['listTableColumns']
        );

        $manager
            ->expects($this->any())
            ->method('listTableColumns')
            ->will(
                $this->returnCallback(
                    function ($table) use ($tableSchema) {
                        return $tableSchema[$table] ?? null;
                    }
                )
            );

        return $manager;
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

    private function mockToolboxFile()
    {
        return $this
            ->getMockBuilder(ToolboxFile::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockStringUtil()
    {
        return $this
            ->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockValidator()
    {
        return $this
            ->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockFileRepository()
    {
        return $this
            ->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockConfig()
    {
        return $this
            ->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Override the method to run the tests on the attribute factories to be tested.
     *
     * @return IAttributeTypeFactory[]
     */
    protected function getAttributeOrderFactories($connection, $tableManipulator)
    {
        return [new AttributeOrderTypeFactory($connection, $tableManipulator)];
    }

    /**
     * Test creation of a file attribute.
     *
     * @return void
     */
    public function testCreateFile()
    {
        $connection   = $this->mockConnection();
        $manipulator  = $this->mockTableManipulator($connection);

        $factory = new AttributeTypeFactory(
            $connection,
            $manipulator,
            $this->mockToolboxFile(),
            $this->mockStringUtil(),
            $this->mockValidator(),
            $this->mockFileRepository(),
            $this->mockConfig()
        );

        $values  = [
            'colname' => 'test'
        ];
        $attribute = $factory->createInstance(
            $values,
            $this->mockMetaModel('mm_test', 'de', 'en')
        );

        $this->assertInstanceOf(File::class, $attribute);

        foreach ($values as $key => $value) {
            $this->assertEquals($value, $attribute->get($key), $key);
        }
    }

    /**
     * Test creation of a file attribute.
     *
     * @return void
     */
    public function testCreateOrderSelect()
    {
        $tableSchema = [
            'mm_test' => [
                'test__sort' => ''
            ]
        ];

        $connection   = $this->mockConnection($this->mockSchemaManager($tableSchema));
        $manipulator  = $this->mockTableManipulator($connection);

        $factory   = new AttributeOrderTypeFactory($connection, $manipulator);
        $values    = [
            'colname' => 'test__sort'
        ];
        $attribute = $factory->createInstance(
            $values,
            $this->mockMetaModel('mm_test', 'de', 'en')
        );

        $this->assertInstanceOf(FileOrder::class, $attribute);
    }
}
