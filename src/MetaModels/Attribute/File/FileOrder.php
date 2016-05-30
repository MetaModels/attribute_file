<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2015 The MetaModels team.
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
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\File;

use MetaModels\Attribute\BaseSimple;
use MetaModels\Helper\ToolboxFile;

/**
 * FileOrder is a helper attribute for the file attribute.
 *
 * @package MetaModels\Attribute\File
 */
class FileOrder extends BaseSimple
{
    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($arrOverrides = array())
    {
        $arrFieldDef = parent::getFieldDefinition($arrOverrides);

        $arrFieldDef['inputType'] = 'fileTreeOrder';

        return $arrFieldDef;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDataType()
    {
        return 'blob NULL';
    }

    /**
     * {@inheritdoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        $varValue = $this->unserializeData($varValue);

        return $varValue;
    }

    /**
     * {@inheritdoc}
     */
    public function parseValue($arrRowData, $strOutputFormat = 'text', $objSettings = null)
    {
        $arrResult = array(
            'raw'  => $arrRowData[$this->getColName()],
            'text' => implode(', ', array_map('String::binToUuid', array_filter($arrRowData[$this->getColName()])))
        );

        return $arrResult;
    }


    /**
     * {@inheritdoc}
     */
    public function unserializeData($value)
    {
        return ToolboxFile::convertValuesToMetaModels(deserialize($value, true));
    }

    /**
     * {@inheritdoc}
     */
    public function serializeData($value)
    {
        if ($value === null) {
            $value = array('bin' => array(), 'value' => array(), 'path' => array());
        }
        $arrData = ToolboxFile::convertValuesToDatabase($value);

        return serialize($arrData);
    }
}
