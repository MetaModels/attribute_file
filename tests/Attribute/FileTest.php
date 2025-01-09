<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Test\Attribute;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Image\ImageFactoryInterface;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
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
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FileTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $tableName The table name.
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
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(\array_merge($methods, ['getDatabasePlatform']))
            ->getMock();

        $platform = $this
            ->getMockBuilder(AbstractPlatform::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMockForAbstractClass();
        $connection->method('getDatabasePlatform')->willReturn($platform);

        return $connection;
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
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
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
            $file->widgetToValue([], 1)
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSearchForFileName()
    {
        $metaModel   = $this->mockMetaModel('mm_test', 'en');
        $connection  = $this->mockConnection(['createQueryBuilder']);
        $manipulator = $this->mockTableManipulator($connection);

        $result1 = $this
            ->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchAllAssociative'])
            ->getMock();
        $result1
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn(
                [
                    [
                        'pid'  => StringUtil::uuidToBin('b4a3201a-bef2-153c-85ae-66930f01feda'),
                        'uuid' => StringUtil::uuidToBin('e68feb56-339b-1eb2-a675-7a5107362e40'),
                    ],
                    [
                        'pid'  => StringUtil::uuidToBin('b4a3201a-bef2-153c-85ae-66930f01feda'),
                        'uuid' => StringUtil::uuidToBin('6e38171a-47c3-1e91-83b4-b759ede063be'),
                    ],
                    [
                        'pid'  => StringUtil::uuidToBin('314f23ae-30ce-11bb-bbd3-2009656507f7'),
                        'uuid' => StringUtil::uuidToBin('0e9e4236-2468-1bfa-89f8-ca45602bec2a'),
                    ],
                ]
            );

        $builder1 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$connection])
            ->onlyMethods(['executeQuery', 'expr'])
            ->getMock();

        $builder1->expects(self::once())->method('expr')->willReturn(new ExpressionBuilder($connection));
        $builder1
            ->expects(self::once())
            ->method('executeQuery')
            ->willReturn($result1);

        $result2 = $this
            ->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchFirstColumn'])
            ->getMock();
        $result2
            ->expects(self::once())
            ->method('fetchFirstColumn')
            ->willReturn([1, 2, 3, 4, 5]);

        $builder2 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$connection])
            ->onlyMethods(['executeQuery'])
            ->getMock();

        $builder2
            ->expects(self::once())
            ->method('executeQuery')
            ->willReturn($result2);

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

        self::assertSame(
            'SELECT f.uuid, f.pid FROM tl_files f WHERE f.name LIKE :value',
            $builder1->getSQL()
        );
        self::assertSame(['value' => '%test_value'], $builder1->getParameters());

        self::assertSame(
            'SELECT t.id FROM mm_test t WHERE ' .
            '(t.file_attribute LIKE :value_0)' .
            ' OR (t.file_attribute LIKE :value_1)' .
            ' OR (t.file_attribute LIKE :value_2)' .
            ' OR (t.file_attribute LIKE :value_3)' .
            ' OR (t.file_attribute LIKE :value_4)',
            $builder2->getSQL()
        );
        self::assertSame(
            [
            'value_0' => '%' . StringUtil::uuidToBin('b4a3201a-bef2-153c-85ae-66930f01feda') . '%',
            'value_1' => '%' . StringUtil::uuidToBin('e68feb56-339b-1eb2-a675-7a5107362e40') . '%',
            'value_2' => '%' . StringUtil::uuidToBin('6e38171a-47c3-1e91-83b4-b759ede063be') . '%',
            'value_3' => '%' . StringUtil::uuidToBin('314f23ae-30ce-11bb-bbd3-2009656507f7') . '%',
            'value_4' => '%' . StringUtil::uuidToBin('0e9e4236-2468-1bfa-89f8-ca45602bec2a') . '%',
            ],
            $builder2->getParameters()
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSearchForUuid()
    {
        $metaModel   = $this->mockMetaModel('mm_test', 'en');
        $connection  = $this->mockConnection(['createQueryBuilder']);
        $manipulator = $this->mockTableManipulator($connection);

        $result1 = $this
            ->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchAllAssociative'])
            ->getMock();
        $result1
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->willReturn(
                [
                    [
                        'pid'  => StringUtil::uuidToBin('b4a3201a-bef2-153c-85ae-66930f01feda'),
                        'uuid' => StringUtil::uuidToBin('e68feb56-339b-1eb2-a675-7a5107362e40'),
                    ],
                ]
            );

        $builder1 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$connection])
            ->onlyMethods(['executeQuery'])
            ->getMock();

        $builder1
            ->expects(self::once())
            ->method('executeQuery')
            ->willReturn($result1);

        $result2 = $this
            ->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fetchFirstColumn'])
            ->getMock();
        $result2
            ->expects(self::once())
            ->method('fetchFirstColumn')
            ->willReturn([1, 2, 3, 4, 5]);

        $builder2 = $this
            ->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$connection])
            ->onlyMethods(['executeQuery'])
            ->getMock();

        $builder2
            ->expects(self::once())
            ->method('executeQuery')
            ->willReturn($result2);

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

        self::assertSame(['1', '2', '3', '4', '5'], $file->searchFor('*e68feb56-339b-1eb2-a675-7a5107362e40*'));

        self::assertSame(
            ['value' => StringUtil::uuidToBin('e68feb56-339b-1eb2-a675-7a5107362e40')],
            $builder1->getParameters()
        );
        self::assertSame(
            'SELECT f.uuid, f.pid FROM tl_files f WHERE f.uuid = :value',
            $builder1->getSQL()
        );

        self::assertSame(
            'SELECT t.id FROM mm_test t WHERE ' .
            '(t.file_attribute LIKE :value_0)' .
            ' OR (t.file_attribute LIKE :value_1)',
            $builder2->getSQL()
        );

        self::assertSame(
            [
                'value_0' => '%' . StringUtil::uuidToBin('b4a3201a-bef2-153c-85ae-66930f01feda') . '%',
                'value_1' => '%' . StringUtil::uuidToBin('e68feb56-339b-1eb2-a675-7a5107362e40') . '%',
            ],
            $builder2->getParameters()
        );
    }
}
