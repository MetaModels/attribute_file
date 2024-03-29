<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2022 The MetaModels team.
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
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\Attribute;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Image\ImageFactoryInterface;
use Doctrine\DBAL\Connection;
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
        $metaModel = $this->getMockForAbstractClass(IMetaModel::class);

        $metaModel
            ->expects(self::any())
            ->method('getTableName')
            ->willReturn($tableName);

        $metaModel
            ->expects(self::any())
            ->method('getActiveLanguage')
            ->willReturn($language);

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

        self::assertInstanceOf(File::class, $file);
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

        self::assertEquals(
            ['bin' => [], 'value' => [], 'path' => [], 'meta' => []],
            $file->widgetToValue(null, 1)
        );
        self::assertEquals(
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
        $connection   = $this->mockConnection(['createQueryBuilder']);
        $manipulator  = $this->mockTableManipulator($connection);

        $statement = $this
            ->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(['fetchAll'])
            ->getMock();
        $statement
            ->expects(self::once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_COLUMN)
            ->willReturn(['1', '2', '3', '4', '5']);

        $builder1 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['expr'])
            ->getMock();

        $builder1->expects(self::once())->method('expr')->willReturn(new ExpressionBuilder($connection));

        $builder2 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute', 'expr'])
            ->getMock();
        $builder2->expects(self::once())->method('expr')->willReturn(new ExpressionBuilder($connection));
        $builder2->expects(self::once())->method('execute')->willReturn($statement);

        $connection
            ->expects(self::exactly(2))
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

        self::assertSame(['1', '2', '3', '4', '5'], $file->searchFor('*test?value'));

        /** @var QueryBuilder $builder2 */
        self::assertSame(
            'SELECT t.id FROM mm_test t WHERE file_attribute IN (SELECT f.uuid FROM tl_files f WHERE f.path LIKE :value)',
            $builder2->getSQL()
        );
        self::assertSame(['value' => '%test_value'], $builder2->getParameters());
    }
}
