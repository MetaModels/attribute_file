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
 * @author     David Greminger <david.greminger@1up.io>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\Attribute;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Image\ImageFactoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\Helper\TableManipulator;
use MetaModels\Helper\ToolboxFile;
use MetaModels\IMetaModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests to test class File.
 *
 * @covers \MetaModels\AttributeFileBundle\Attribute\File
 */
class FileTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $tableName The table name.
     *
     * @param string $language  The language.
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
     * @param array $methods The method names to mock.
     *
     * @return MockObject|Connection
     */
    private function mockConnection($methods = [])
    {
        return $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mock the table manipulator.
     *
     * @param Connection $connection The database connection mock.
     *
     * @return TableManipulator|MockObject
     */
    private function mockTableManipulator(Connection $connection)
    {
        return $this->getMockBuilder(TableManipulator::class)
            ->setConstructorArgs([$connection, []])
            ->getMock();
    }

    /**
     * Mock the image factory.
     *
     * @return ImageFactoryInterface|MockObject
     */
    private function mockImageFactory()
    {
        return $this->getMockBuilder(ImageFactoryInterface::class)
            ->getMockForAbstractClass();
    }

    private function mockToolboxFile()
    {
        $toolbox = $this
            ->getMockBuilder(ToolboxFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $toolbox;
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
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $metaModel   = $this->mockMetaModel('en', 'en');
        $connection  = $this->mockConnection();
        $manipulator = $this->mockTableManipulator($connection);

        $file = new File(
            $metaModel,
            [],
            $connection,
            $manipulator,
            $this->mockToolboxFile(),
            $this->mockStringUtil(),
            $this->mockValidator(),
            $this->mockFileRepository(),
            $this->mockConfig()
        );

        $this->assertInstanceOf('MetaModels\AttributeFileBundle\Attribute\File', $file);
    }

    /**
     * Test that empty values are handled correctly.
     *
     * @return void
     */
    public function testEmptyValues()
    {
        $metaModel   = $this->mockMetaModel('en', 'en');
        $connection  = $this->mockConnection();
        $manipulator = $this->mockTableManipulator($connection);

        $file = new File(
            $metaModel,
            ['file_multiple' => false],
            $connection,
            $manipulator,
            $this->mockToolboxFile(),
            $this->mockStringUtil(),
            $this->mockValidator(),
            $this->mockFileRepository(),
            $this->mockConfig()
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

    /**
     * Test the search for method.
     *
     * @return void
     */
    public function testSearchFor()
    {
        $metaModel    = $this->mockMetaModel('mm_test', 'en');
        $connection   = $this->mockConnection(['createQueryBuilder', 'getDatabasePlatform']);
        $manipulator  = $this->mockTableManipulator($connection);

        $platform = $this->getMockForAbstractClass(AbstractPlatform::class);
        $connection
            ->expects($this->once())
            ->method('getDatabasePlatform')
            ->willReturn($platform);

        $statement = $this
            ->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(['fetchAll'])
            ->getMock();
        $statement
            ->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_COLUMN)
            ->willReturn(['1', '2', '3', '4', '5']);

        $builder1 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['expr'])
            ->getMock();

        $builder1->expects($this->once())->method('expr')->willReturn(new ExpressionBuilder($connection));

        $builder2 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute', 'expr'])
            ->getMock();
        $builder2->expects($this->once())->method('expr')->willReturn(new ExpressionBuilder($connection));
        $builder2->expects($this->once())->method('execute')->willReturn($statement);

        $connection
            ->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->willReturnOnConsecutiveCalls($builder1, $builder2);

        $file = new File(
            $metaModel,
            [
                'colname'       => 'file_attribute',
                'file_multiple' => false
            ],
            $connection,
            $manipulator,
            $this->mockToolboxFile(),
            $this->mockStringUtil(),
            $this->mockValidator(),
            $this->mockFileRepository(),
            $this->mockConfig()
        );

        $this->assertSame(['1', '2', '3', '4', '5'], $file->searchFor('*test?value'));

        /** @var QueryBuilder $builder2 */
        $this->assertSame(
            'SELECT id FROM mm_test WHERE file_attribute IN (SELECT uuid FROM tl_files WHERE path LIKE :value)',
            $builder2->getSQL()
        );
        $this->assertSame(['value' => '%test_value'], $builder2->getParameters());
    }
}
