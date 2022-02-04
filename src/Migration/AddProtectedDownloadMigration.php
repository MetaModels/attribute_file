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
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\TableDiff;
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
     * @param Connection       $connection The database connection.
     * @param TableManipulator $tableManipulator
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
        return 'Add "file_protectedDownload" in MetaModels rendersettings if not exist '
        . 'and set to checked if "file_showLink" is set as backward compatibility.';
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

        if (!$schemaManager->tablesExist(['tl_metamodel', 'tl_metamodel_rendersettings'])) {
            return false;
        }

        if (!$this->fieldExists('tl_metamodel_rendersettings', 'file_protectedDownload')) {
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
        $schemaManager = $this->connection->getSchemaManager();

        if (!$this->fieldExists('tl_metamodel_rendersettings', 'file_showLink')) {
            $this->tableManipulator->createColumn(
                'tl_metamodel_rendersettings',
                'file_protectedDownload',
                'char(1) NOT NULL default \'\''
            );
        }

        if ($this->fieldExists('tl_metamodel_rendersettings', 'file_protectedDownload')) {
            $this->connection->createQueryBuilder()
                ->update('tl_metamodel_rendersettings', 't')
                ->set('t.file_protectedDownload', 't.file_showLink')
                ->execute();

            $this->tableManipulator->dropColumn('tl_metamodel_attribute', 'get_land');
        }

        return new MigrationResult(true, 'Adjusted table tl_metamodel_rendersettings with file_protectedDownload');
    }

    /**
     * Check is a table column exists.
     *
     * @param string $strTableName  Table name.
     * @param string $strColumnName Column name.
     *
     * @return bool
     */
    private function fieldExists($strTableName, $strColumnName)
    {
        $columns = $this->connection->getSchemaManager()->listTableColumns($strTableName);

        return isset($columns[$strColumnName]);
    }
}
