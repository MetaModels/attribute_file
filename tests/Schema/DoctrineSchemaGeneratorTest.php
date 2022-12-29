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
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types = 1);

namespace MetaModels\AttributeFileBundle\Test\Schema;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use MetaModels\AttributeFileBundle\Schema\DoctrineSchemaGenerator;
use MetaModels\Information\AttributeInformation;
use PHPUnit\Framework\TestCase;

/**
 * This tests the schema generator.
 */
class DoctrineSchemaGeneratorTest extends TestCase
{
    /**
     * Test the generate method.
     *
     * @return void
     */
    public function testGenerate(): void
    {
        $instance   = new DoctrineSchemaGenerator();
        $reflection = new \ReflectionMethod(DoctrineSchemaGenerator::class, 'generateAttribute');
        $reflection->setAccessible(true);

        $tableSchema = new Table('mm_test');
        $attribute   = new AttributeInformation('test', 'file', ['file_multiple' => false]);

        $reflection->invoke($instance, $tableSchema, $attribute);

        $this->assertTrue($tableSchema->hasColumn('test'));
        $column = $tableSchema->getColumn('test');
        $this->assertSame('test', $column->getName());
        $this->assertSame(Type::getType(Types::BLOB), $column->getType());
        $this->assertSame(false, $column->getNotnull());

        $this->assertFalse($tableSchema->hasColumn('test__sort'));
    }

    /**
     * Test the generate method.
     *
     * @return void
     */
    public function testGenerateForMultiple(): void
    {
        $instance   = new DoctrineSchemaGenerator();
        $reflection = new \ReflectionMethod(DoctrineSchemaGenerator::class, 'generateAttribute');
        $reflection->setAccessible(true);

        $tableSchema = new Table('mm_test');
        $attribute   = new AttributeInformation('test', 'file', ['file_multiple' => true]);

        $reflection->invoke($instance, $tableSchema, $attribute);

        $this->assertTrue($tableSchema->hasColumn('test'));
        $column = $tableSchema->getColumn('test');
        $this->assertSame('test', $column->getName());
        $this->assertSame(Type::getType(Types::BLOB), $column->getType());
        $this->assertSame(false, $column->getNotnull());

        $this->assertTrue($tableSchema->hasColumn('test__sort'));
        $column = $tableSchema->getColumn('test__sort');
        $this->assertSame('test__sort', $column->getName());
        $this->assertSame(Type::getType(Types::BLOB), $column->getType());
        $this->assertSame(false, $column->getNotnull());
    }
}
