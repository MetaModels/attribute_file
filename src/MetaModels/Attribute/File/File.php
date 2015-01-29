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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Christopher Boelter <c.boelter@cogizz.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     David Maack <david.maack@arcor.de>
 * @author     David Maack <maack@men-at-work.de>
 * @author     MrTool <github@r2pi.de>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\File;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ManipulateWidgetEvent;
use MetaModels\Attribute\BaseSimple;
use MetaModels\DcGeneral\Events\WizardHandler;
use MetaModels\Render\Template;
use MetaModels\Helper\ToolboxFile;

/**
 * This is the MetaModel attribute class for handling file fields.
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class File extends BaseSimple
{
    /**
     * {@inheritdoc}
     */
    public function searchFor($strPattern)
    {
        // Base implementation, do a simple search on given column.
        $objQuery = \Database::getInstance()
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
                \FilesModel::getTable()
            ))
            ->execute(str_replace(array('*', '?'), array('%', '_'), $strPattern));

        $arrIds = $objQuery->fetchEach('id');

        return $arrIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDataType()
    {
        if (version_compare(VERSION, '3.2', '<')) {
            return 'text NULL';
        }

        return 'blob NULL';
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSettingNames()
    {
        return array_merge(parent::getAttributeSettingNames(), array(
            'file_multiple',
            'file_customFiletree',
            'file_uploadFolder',
            'file_validFileTypes',
            'file_filesOnly',
            'file_filePicker',
            'filterable',
            'searchable',
            'mandatory',
        ));
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
        $arrValues = deserialize($value, true);

        $arrReturn = array();
        foreach ($arrValues as $mixValue) {
            $arrReturn['value'][] = (version_compare(VERSION, '3.2', '>='))
                ? \String::binToUuid($mixValue)
                : $mixValue;
            $arrReturn['path'][]  = \FilesModel::findByPk($mixValue)->path;
        }

        return $arrReturn;
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
        if (is_array($mixValues)) {
            $arrData = array();
            // Check if we have a array with value and path.
            if (array_key_exists('value', $mixValues)) {
                foreach ($mixValues['value'] as $mixValue) {
                    $arrData[] = \String::uuidToBin($mixValue);
                }
            } else {
                // Else run just as a normal array.
                foreach ($mixValues as $mixValue) {
                    $arrData[] = $mixValue;
                }
            }

            // Check single file or multiple file.
            if ($this->get('file_multiple')) {
                $mixValues = serialize($arrData);
            } else {
                $mixValues = $arrData[0];
            }
        }

        return $mixValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($arrOverrides = array())
    {
        $arrFieldDef = parent::getFieldDefinition($arrOverrides);

        $arrFieldDef['inputType']          = 'fileTree';
        $arrFieldDef['eval']['files']      = true;
        $arrFieldDef['eval']['fieldType']  = $this->get('file_multiple') ? 'checkbox' : 'radio';
        $arrFieldDef['eval']['multiple']   = $this->get('file_multiple') ? true : false;
        $arrFieldDef['eval']['extensions'] = $GLOBALS['TL_CONFIG']['allowedDownload'];

        if ($this->get('file_customFiletree')) {
            if (strlen($this->get('file_uploadFolder'))) {
                // Set root path of file chooser depending on contao version.
                $objFile = null;

                if (strlen($this->get('file_uploadFolder')) == 16) {
                    // If not numeric we have a Contao 3.2.x with a binary uuid value.
                    $objFile = \FilesModel::findByUuid($this->get('file_uploadFolder'));
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

        // Set all options for the file picker.
        if (version_compare(VERSION, '3.3', '<') && $this->get('file_filePicker') && !$this->get('file_multiple')) {
            $arrFieldDef['inputType']         = 'text';
            $arrFieldDef['eval']['tl_class'] .= ' wizard';

            $dispatcher = $this->getMetaModel()->getServiceContainer()->getEventDispatcher();
            $dispatcher->addListener(
                ManipulateWidgetEvent::NAME,
                array(new WizardHandler($this->getMetaModel(), $this->getColName()), 'getWizard')
            );
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

        if (version_compare(VERSION, '3.3', '>=') || !$this->get('file_filePicker')) {
            if (!is_array($varValue)) {
                return $varValue;
            }

            return serialize($varValue['value']);
        }

        // If we get a numeric id, it is the correct value.
        if (!is_array($varValue)) {
            $strValue = $varValue;
        } else {
            $strValue = is_array($varValue['value']) ? $varValue['value'][0] : $varValue['value'];
        }

        $objToolbox = new ToolboxFile();

        return $objToolbox->convertValueToPath($strValue);
    }

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart - We do not need the parameter $intId.
    public function widgetToValue($varValue, $itemId)
    {
        if (version_compare(VERSION, '3.3', '<') && ($this->get('file_filePicker'))) {
            $objFile = \Dbafs::addResource($varValue);

            return $objFile->uuid;
        }

        return parent::valueToWidget($varValue);
    }
    // @codingStandardsIgnoreEnd

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
