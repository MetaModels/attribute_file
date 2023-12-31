<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2023 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Kim Wormer <info@kim-wormer.de>
 * @copyright  2012-2023 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeFileBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use MetaModels\Helper\TableManipulator;

/**
 * This migration add column *__sort for every
 * file attribute column if option multiple set.
 */
class AddSortFieldMigration extends AbstractMigration
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * Table manipulator.
     *
     * @var TableManipulator
     */
    protected TableManipulator $tableManipulator;

    /**
     * Create a new instance.
     *
     * @param Connection       $connection       The database connection.
     * @param TableManipulator $tableManipulator The table manipulator.
     */
    public function __construct(Connection $connection, TableManipulator $tableManipulator)
    {
        $this->connection       = $connection;
        $this->tableManipulator = $tableManipulator;
    }

    /**
     * Return the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Add column *__sort for every file attribute column if option multiple set.';
    }

    /**
     * Must only run if:
     * - the MM tables are present AND
     * - there are some columns defined
     *
     * @return bool
     *
     * @throws Exception
     */
    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_metamodel', 'tl_metamodel_attribute'])) {
            return false;
        }

        if (!$this->fieldExists('tl_metamodel_attribute', 'file_multiple')) {
            return false;
        }

        if ($this->countMissingSortColumns($this->getFileAttributes())) {
            return true;
        }

        return false;
    }

    /**
     * Create the missing columns *__sort for attribute file.
     *
     * @return MigrationResult
     *
     * @throws Exception
     */
    public function run(): MigrationResult
    {
        $attributes = $this->getFileAttributes();

        if (
            !$this->fieldExists('tl_metamodel_attribute', 'file_multiple')
            && !$attributes->rowCount()
        ) {
            return new MigrationResult(true, 'Nothing to do.');
        }

        $messages = [];
        while ($row = $attributes->fetchAssociative()) {
            if ($this->fieldExists($row['tableName'], $row['colname'] . '__sort')) {
                continue;
            }

            $this->tableManipulator->createColumn(
                $row['tableName'],
                $row['colname'] . '__sort',
                'blob NULL'
            );

            $messages[] = \sprintf('%s: %s__sort', $row['tableName'], $row['colname']);
        }

        return new MigrationResult(
            true,
            \sprintf('Add columns for attribute file: %s', \implode(', ', $messages))
        );
    }

    /**
     * Get file attributes.
     *
     * @return Result Returns database result.
     *
     * @throws \Doctrine\DBAL\Exception The DBAL exception.
     */
    private function getFileAttributes(): Result
    {
        return $this
            ->connection
            ->createQueryBuilder()
            ->select('metamodel.tableName, attribute.colname')
            ->from('tl_metamodel_attribute', 'attribute')
            ->leftJoin('attribute', 'tl_metamodel', 'metamodel', 'metamodel.id=attribute.pid')
            ->where('attribute.type=:type')
            ->setParameter('type', 'file')
            ->andWhere('attribute.file_multiple=:multiple')
            ->setParameter('multiple', '1')
            ->executeQuery();
    }

    /**
     * Count missing sort columns.
     *
     * @param Result $attributes The attributes.
     *
     * @return int Returns columns count.
     *
     * @throws Exception
     */
    private function countMissingSortColumns(Result $attributes): int
    {
        $countColumns = 0;
        $rows         = $attributes->fetchAllAssociative();

        foreach ($rows as $row) {
            if (
                !$this->fieldExists($row['tableName'], $row['colname'])
                || $this->fieldExists($row['tableName'], $row['colname'] . '__sort')
            ) {
                continue;
            }

            $countColumns++;
        }

        return $countColumns;
    }

    /**
     * Check if a table column exists.
     *
     * @param string $tableName  Table name.
     * @param string $columnName Column name.
     *
     * @return bool
     *
     * @throws Exception
     */
    private function fieldExists(string $tableName, string $columnName): bool
    {
        $columns = $this->connection->createSchemaManager()->listTableColumns($tableName);

        return isset($columns[\strtolower($columnName)]);
    }
}
