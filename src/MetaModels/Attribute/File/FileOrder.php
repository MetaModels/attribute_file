<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\File;

use MetaModels\Attribute\BaseSimple;

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
        if (!is_array($value)) {
            $deserialized = deserialize($value);

            if (is_array($deserialized)) {
                $value = $deserialized;
            } else {
                $value = explode(',', $value);
            }
        }

        return array_map(
            function ($value) {
                if (!\Validator::isBinaryUuid($value)) {
                    $value = \String::uuidToBin($value);
                }

                return $value;
            },
            $value
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serializeData($value)
    {
        return serialize($value);
    }
}
