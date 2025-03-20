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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2023 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Attribute;

use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\ISimple;
use MetaModels\Attribute\IInternal;
use MetaModels\IMetaModel;

/**
 * FileOrder is a helper attribute for the file attribute.
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
    private IMetaModel $metaModel;

    /**
     * The column name.
     *
     * @var string
     */
    private string $colName;

    /**
     * The connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * Create a new instance.
     *
     * @param IMetaModel $metaModel   The MetaModel.
     * @param array      $information The attribute information.
     * @param Connection $connection  The connection.
     */
    public function __construct(IMetaModel $metaModel, array $information, Connection $connection)
    {
        $this->metaModel  = $metaModel;
        $this->colName    = $information['colname'];
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return '';
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
        return $this->unserializeData($varValue);
    }

    /**
     * {@inheritDoc}
     */
    public function setDataFor($arrValues)
    {
        foreach ($arrValues as $id => $value) {
            $this->connection
                ->createQueryBuilder()
                ->update($this->getMetaModel()->getTableName(), 't')
                ->set('t.' . $this->getColName(), ':' . $this->getColName())
                ->where('t.id=:id')
                ->setParameter($this->getColName(), $value ?: $this->serializeData([]))
                ->setParameter('id', $id)
                ->executeQuery();
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
        return StringUtil::deserialize($value, true);
    }

    /**
     * {@inheritDoc}
     */
    public function serializeData($value)
    {
        return \serialize($value);
    }
}
