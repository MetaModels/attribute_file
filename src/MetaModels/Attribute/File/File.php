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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Christopher Boelter <c.boelter@cogizz.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     David Maack <david.maack@arcor.de>
 * @author     David Maack <maack@men-at-work.de>
 * @author     MrTool <github@r2pi.de>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Marc Reimann <reimann@mediendepot-ruhr.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Attribute\File;

use Contao\Config;
use Contao\Database;
use Contao\FilesModel;
use Contao\Validator;
use MetaModels\Attribute\BaseSimple;
use MetaModels\Helper\ToolboxFile;
use MetaModels\Render\Template;

/**
 * This is the MetaModel attribute class for handling file fields.
 */
class File extends BaseSimple
{
    /**
     * {@inheritdoc}
     */
    public function searchFor($strPattern)
    {
        // Base implementation, do a simple search on given column.
        $objQuery = Database::getInstance()
            ->prepare(sprintf(
                'SELECT id
                    FROM %s
                    WHERE %s IN
                    (SELECT uuid FROM
                    %s
                    WHERE path
                    LIKE
                    ?)',
                $this->getMetaModel()->getTableName(),
                $this->getColName(),
                FilesModel::getTable()
            ))
            ->execute(str_replace(['*', '?'], ['%', '_'], $strPattern));

        $arrIds = $objQuery->fetchEach('id');

        return $arrIds;
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
    public function getAttributeSettingNames()
    {
        return array_merge(
            parent::getAttributeSettingNames(),
            [
                'file_multiple',
                'file_customFiletree',
                'file_uploadFolder',
                'file_validFileTypes',
                'file_filesOnly',
                'filterable',
                'searchable',
                'mandatory',
            ]
        );
    }

    /**
     * Take the raw data from the DB column and unserialize it.
     *
     * @param mixed $value The array of data from the database.
     *
     * @return array
     */
    public function unserializeData($value)
    {
        return ToolboxFile::convertValuesToMetaModels(deserialize($value, true));
    }

    /**
     * Take the data from the system and serialize it for the database.
     *
     * @param mixed $mixValues The data to serialize.
     *
     * @return string An serialized array with binary data or a binary data.
     */
    public function serializeData($mixValues)
    {
        if ($mixValues === null) {
            $mixValues = ['bin' => [], 'value' => [], 'path' => []];
        }
        $arrData = ToolboxFile::convertValuesToDatabase($mixValues);

        // Check single file or multiple file.
        if ($this->get('file_multiple')) {
            $mixValues = serialize($arrData);
        } else {
            $mixValues = $arrData[0];
        }

        return $mixValues;
    }

    /**
     * Manipulate the field definition for custom file trees.
     *
     * @param array $arrFieldDef The field definition to manipulate.
     *
     * @return void
     */
    private function handleCustomFileTree(&$arrFieldDef)
    {
        if (strlen($this->get('file_uploadFolder'))) {
            // Set root path of file chooser depending on contao version.
            $objFile = null;

            if (Validator::isUuid($this->get('file_uploadFolder'))) {
                $objFile = FilesModel::findByUuid($this->get('file_uploadFolder'));
            }

            // Check if we have a file.
            if ($objFile != null) {
                $arrFieldDef['eval']['path'] = $objFile->path;
            } else {
                // Fallback.
                $arrFieldDef['eval']['path'] = $this->get('file_uploadFolder');
            }
        }

        if (strlen($this->get('file_validFileTypes'))) {
            $arrFieldDef['eval']['extensions'] = $this->get('file_validFileTypes');
        }

        if (strlen($this->get('file_filesOnly'))) {
            $arrFieldDef['eval']['filesOnly'] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($arrOverrides = [])
    {
        $arrFieldDef = parent::getFieldDefinition($arrOverrides);

        $arrFieldDef['inputType']          = 'fileTree';
        $arrFieldDef['eval']['files']      = true;
        $arrFieldDef['eval']['extensions'] = Config::get('allowedDownload');
        $arrFieldDef['eval']['multiple']   = (bool) $this->get('file_multiple');

        if ($this->get('file_multiple')) {
            $arrFieldDef['eval']['fieldType'] = 'checkbox';
        } else {
            $arrFieldDef['eval']['fieldType'] = 'radio';
        }

        if ($this->get('file_customFiletree')) {
            $this->handleCustomFileTree($arrFieldDef);
        }

        return $arrFieldDef;
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        if (empty($varValue)) {
            return null;
        }

        if (!$this->get('file_multiple')) {
            return isset($varValue['bin'][0]) ? $varValue['bin'][0] : null;
        }

        return $varValue['bin'];
    }

    /**
     * {@inheritdoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        return ToolboxFile::convertUuidsOrPathsToMetaModels((array) $varValue);
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareTemplate(Template $objTemplate, $arrRowData, $objSettings)
    {
        parent::prepareTemplate($objTemplate, $arrRowData, $objSettings);

        $objToolbox = new ToolboxFile();

        $objToolbox->setBaseLanguage($this->getMetaModel()->getActiveLanguage());

        $objToolbox->setFallbackLanguage($this->getMetaModel()->getFallbackLanguage());

        $objToolbox->setLightboxId(sprintf(
            '%s.%s.%s',
            $this->getMetaModel()->getTableName(),
            $objSettings->get('id'),
            $arrRowData['id']
        ));

        if (strlen($this->get('file_validFileTypes'))) {
            $objToolbox->setAcceptedExtensions($this->get('file_validFileTypes'));
        }

        $objToolbox->setShowImages($objSettings->get('file_showImage'));

        if ($objSettings->get('file_imageSize')) {
            $objToolbox->setResizeImages($objSettings->get('file_imageSize'));
        }

        if ($arrRowData[$this->getColName()]) {
            $value = $arrRowData[$this->getColName()];

            if (isset($value['value'])) {
                foreach ($value['value'] as $strFile) {
                    $objToolbox->addPathById($strFile);
                }
            } elseif (is_array($value)) {
                foreach ($value as $strFile) {
                    $objToolbox->addPathById($strFile);
                }
            } else {
                $objToolbox->addPathById($value);
            }
        }

        $objToolbox->resolveFiles();
        $arrData = $objToolbox->sortFiles($objSettings->get('file_sortBy'));

        $objTemplate->files = $arrData['files'];
        $objTemplate->src   = $arrData['source'];
    }
}
