<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Attribute;

use Doctrine\DBAL\Connection;
use MetaModels\Attribute\ISimple;
use MetaModels\Attribute\IInternal;
use MetaModels\IMetaModel;

/**
 * FileOrder is a helper attribute for the file attribute.
 *
 * @package MetaModels\Attribute\File
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FileOrder implements ISimple, IInternal
{
    /**
     * The MetaModel in use.
     *
     * @var IMetaModel
     */
    private $metaModel = null;

    /**
     * The column name.
     *
     * @var string
     */
    private $colName;

    /**
     * The connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Create a new instance.
     *
     * @param null       $metaModel
     * @param string     $colName
     * @param Connection $connection
     */
    public function __construct($metaModel, $colName, Connection $connection)
    {
        $this->metaModel  = $metaModel;
        $this->colName    = $colName;
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getColName()
    {
        return $this->colName;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaModel()
    {
        return $this->metaModel;
    }

    /**
     * {@inheritDoc}
     */
    public function get($strKey)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function set($strKey, $varValue)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function handleMetaChange($strMetaName, $varNewValue)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function destroyAUX()
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function initializeAUX()
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function getAttributeSettingNames()
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function getFieldDefinition($arrOverrides = array())
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function getItemDCA($arrOverrides = array())
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     */
    public function valueToWidget($varValue)
    {
        return $varValue;
    }

    /**
     * {@inheritDoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        $varValue = $this->unserializeData($varValue);

        return $varValue;
    }

    /**
     * {@inheritDoc}
     */
    public function setDataFor($arrValues)
    {
        foreach ($arrValues as $id => $varData) {
            if ($varData === null) {
                $varData = $this->serializeData([]);
            }

            $this->connection->update(
                $this->getMetaModel()->getTableName(),
                [$this->getColName() => $varData],
                ['id' => $id]
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function getDefaultRenderSettings()
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function parseValue($arrRowData, $strOutputFormat = 'text', $objSettings = null)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function getFilterUrlValue($varValue)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function sortIds($idList, $strDirection)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function getFilterOptions($idList, $usedOnly, &$arrCount = null)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function searchFor($strPattern)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function filterGreaterThan($varValue, $blnInclusive = false)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function filterLessThan($varValue, $blnInclusive = false)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function filterNotEqual($varValue)
    {
        throw new \LogicException(__METHOD__ . ' is a virtual helper attribute and not intended for use.');
    }
    /**
     * {@inheritDoc}
     */
    public function modelSaved($objItem)
    {
        // No op.
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function getSQLDataType()
    {
        throw new \LogicException('FileOrder is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function createColumn()
    {
        throw new \LogicException('FileOrder is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function deleteColumn()
    {
        throw new \LogicException('FileOrder is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \LogicException Always, as this is not intended for real use.
     */
    public function renameColumn($strNewColumnName)
    {
        throw new \LogicException('FileOrder is a virtual helper attribute and not intended for use.');
    }

    /**
     * {@inheritDoc}
     */
    public function unserializeData($value)
    {
        return deserialize($value, true);
    }

    /**
     * {@inheritDoc}
     */
    public function serializeData($value)
    {
        return serialize($value);
    }
}
