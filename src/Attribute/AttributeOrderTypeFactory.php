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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Benedict Zinke <bz@presentprogressive.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Attribute;

use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Helper\TableManipulator;

/**
 * Attribute type factory for file order attributes.
 */
class AttributeOrderTypeFactory implements IAttributeTypeFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Table manipulator.
     *
     * @var TableManipulator
     */
    protected $tableManipulator;

    /**
     * Cache table columns.
     * 
     * @var array
     */
    private $tableColumns = [];

    /**
     * {@inheritDoc}
     *
     * @param Connection       $connection       The database connection.
     * @param TableManipulator $tableManipulator The table manipulator.
     * @param string           $rootPath         The root path.
     */
    public function __construct(
        Connection $connection,
        TableManipulator $tableManipulator
    ) {
        $this->connection       = $connection;
        $this->tableManipulator = $tableManipulator;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeName()
    {
        return 'filesort';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeIcon()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $metaModel)
    {
        $columnName = ($information['colname'] ?? null);
        $tableName = $metaModel->getTableName();

        if (!$this->tableColumns[$tableName]) {
            $this->tableColumns[$tableName] = $this->connection->getSchemaManager()->listTableColumns($tableName);
        }

        if (!$columnName || !\array_key_exists($columnName, $this->tableColumns[$tableName])) {
            return null;
        }

        return new FileOrder($metaModel, $information, $this->connection);
    }

    /**
     * {@inheritDoc}
     */
    public function isTranslatedType()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSimpleType()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isComplexType()
    {
        return false;
    }
}
