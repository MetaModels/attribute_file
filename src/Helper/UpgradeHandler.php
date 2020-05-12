<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2020 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright  2012-2020 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Helper;

use Doctrine\DBAL\Connection;

/**
 * Upgrade handler class that changes structural changes in the database.
 * This should rarely be necessary but sometimes we need it.
 */
class UpgradeHandler
{
    /**
     * The database to use.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The cache of table schemas.
     *
     * @var array
     */
    private $schemaCache = [];

    /**
     * Create a new instance.
     *
     * @param Connection $connection The database connection to use.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Perform all upgrade steps.
     *
     * @return void
     */
    public function perform()
    {
        $this->ensureOrderColumnExists();
    }

    /**
     * Ensure that the order column exists.
     *
     * @return void
     */
    private function ensureOrderColumnExists()
    {
        $attributes = $this
            ->connection
            ->createQueryBuilder()
            ->select('metamodel.tableName', 'attribute.colname')
            ->from('tl_metamodel_attribute', 'attribute')
            ->leftJoin('attribute', 'tl_metamodel', 'metamodel', 'metamodel.id=attribute.pid')
            ->where('attribute.type=:type')
            ->setParameter('type', 'file')
            ->andWhere('attribute.file_multiple=:multiple')
            ->setParameter('multiple', '1')
            ->execute();

        while ($row = $attributes->fetch(\PDO::FETCH_OBJ)) {
            if ($this->fieldExists($row->tableName, $row->colname . '__sort')) {
                continue;
            }
            $this
                ->connection
                ->exec(
                    \sprintf(
                        'ALTER TABLE %1$s ADD COLUMN %2$s__sort %3$s',
                        $row->tableName,
                        $row->colname,
                        'blob NULL'
                    )
                );
        }
    }

    /**
     * Test if a column exists in a table.
     *
     * @param string $tableName  Table name.
     * @param string $columnName Column name.
     *
     * @return bool
     */
    private function fieldExists($tableName, $columnName): bool
    {
        if (!\array_key_exists($tableName, $this->schemaCache)) {
            $this->schemaCache[$tableName] = $this->connection->getSchemaManager()->listTableColumns($tableName);
        }

        return isset($this->schemaCache[$tableName][$columnName]);
    }
}
