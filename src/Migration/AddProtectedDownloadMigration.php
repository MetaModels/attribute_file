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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeFileBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use MetaModels\Helper\TableManipulator;

/**
 * This migration add file_protectedDownload
 * and changes set to 1 if file_showLink is set.
 */
class AddProtectedDownloadMigration extends AbstractMigration
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
        return 'Add checkbox "Protected download" in MetaModels render-settings if not exist and set to checked if ' .
               'checkbox "Create link as file download" is set as backward compatibility. If you do not need this, ' .
               'remove the protection, as no cookies need to be set for this.';
    }

    /**
     * Must only run if:
     * - the MM tables are present AND
     * - there are some columns defined
     *
     * @return bool
     */
    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_metamodel', 'tl_metamodel_rendersetting'])) {
            return false;
        }

        if ($this->fieldExists('tl_metamodel_rendersetting', 'file_showLink')
            && !$this->fieldExists('tl_metamodel_rendersetting', 'file_protectedDownload')) {
            return true;
        }

        return false;
    }

    /**
     * Create the missing columns and copy existing values;
     * drop column get_land manually in install tool.
     *
     * @return MigrationResult
     */
    public function run(): MigrationResult
    {
        if (!$this->fieldExists('tl_metamodel_rendersetting', 'file_protectedDownload')) {
            $this->tableManipulator->createColumn(
                'tl_metamodel_rendersetting',
                'file_protectedDownload',
                'char(1) NOT NULL default \'\''
            );

            $this->connection->createQueryBuilder()
                ->update('tl_metamodel_rendersetting', 't')
                ->set('t.file_protectedDownload', 't.file_showLink')
                ->execute();

            return new MigrationResult(true, 'Adjusted table tl_metamodel_rendersetting with file_protectedDownload');
        }

        return new MigrationResult(true, 'Nothing to do.');
    }

    /**
     * Check is a table column exists.
     *
     * @param string $tableName  Table name.
     * @param string $columnName Column name.
     *
     * @return bool
     */
    private function fieldExists(string $tableName, string $columnName): bool
    {
        $columns = $this->connection->getSchemaManager()->listTableColumns($tableName);

        return isset($columns[strtolower($columnName)]);
    }
}
