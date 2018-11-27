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
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Helper;

use Contao\Database;

/**
 * Upgrade handler class that changes structural changes in the database.
 * This should rarely be necessary but sometimes we need it.
 */
class UpgradeHandler
{
    /**
     * The database to use.
     *
     * @var Database
     */
    private $database;

    /**
     * Create a new instance.
     *
     * @param Database $database The database instance to use.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
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
            ->database
            ->prepare(
                'SELECT metamodel.tableName, attribute.colname
                FROM tl_metamodel_attribute AS attribute
                LEFT JOIN tl_metamodel AS metamodel
                ON (metamodel.id=attribute.pid)
                WHERE attribute.type=?
                AND attribute.file_multiple=?'
            )
            ->execute('file', 1);

        while ($attributes->next()) {
            if ($this->database->fieldExists($attributes->colname . '__sort', $attributes->tableName, true)) {
                continue;
            }
            $this
                ->database
                ->execute(
                    \sprintf(
                        'ALTER TABLE %1$s ADD COLUMN %2$s_sort %3$s',
                        $attributes->tableName,
                        $attributes->colname,
                        'blob NULL'
                    )
                );
        }
    }
}
