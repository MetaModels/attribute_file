<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\File;

use MetaModels\Attribute\BaseSimple;
use MetaModels\Helper\ContaoController;
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
	public function getSQLDataType()
	{
		if(version_compare(VERSION, '3.2', '<'))
		{
			return 'text NULL';
		}
		else
		{
			return 'blob NULL';
		}
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
	 * @param type $value
	 */
	public function unserializeData($value)
	{
		$arrValues = deserialize($value, true);

		if (version_compare(VERSION, '3.0', '>='))
		{
			$arrReturn = array();
			foreach ($arrValues as $mixValue)
			{
				$arrReturn['value'][]	 = (version_compare(VERSION, '3.2', '>=')) ? \String::binToUuid($mixValue) : $mixValue;
				$arrReturn['path'][]	 = \FilesModel::findByPk($mixValue)->path;
			}
			$arrValues = $arrReturn;
		}

		return $arrValues;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFieldDefinition($arrOverrides = array())
	{
		$arrFieldDef=parent::getFieldDefinition($arrOverrides);

		$arrFieldDef['inputType']          = 'fileTree';
		$arrFieldDef['eval']['files']      = true;
		$arrFieldDef['eval']['fieldType']  = $this->get('file_multiple') ? 'checkbox' : 'radio';
		$arrFieldDef['eval']['multiple']   = $this->get('file_multiple') ? true : false;
		$arrFieldDef['eval']['extensions'] = $GLOBALS['TL_CONFIG']['allowedDownload'];

		if ($this->get('file_customFiletree'))
		{
			
			// set root path of file chooser depending on contao version
			if (version_compare(VERSION, '3.0', '<')){
				if (strlen($this->get('file_uploadFolder')))
				{    
					$arrFieldDef['eval']['path'] = $this->get('file_uploadFolder');
				}
			}else{
				// contao 3 stores the pk of the folder so we had to convert them to work
				if (strlen($this->get('file_uploadFolder')) && is_numeric($this->get('file_uploadFolder')))
				{    
					$objFile = \FilesModel::findByPk($this->get('file_uploadFolder'));
					$arrFieldDef['eval']['path'] = $objFile->path;
				}
			
				// fallback if path is not a numeric value - i dont know if needed but i think its better
				if (strlen($this->get('file_uploadFolder')) && !is_numeric($this->get('file_uploadFolder')))
				{
					$arrFieldDef['eval']['path'] = $this->get('file_uploadFolder');
				}
			
			}
			
			if (strlen($this->get('file_validFileTypes')))
			{
				$arrFieldDef['eval']['extensions'] = $this->get('file_validFileTypes');
			}
			if (strlen($this->get('file_filesOnly')))
			{
				$arrFieldDef['eval']['filesOnly'] = true;
			}
		}

		// Set all options for the file picker.
		if($this->get('file_filePicker') && !$this->get('file_multiple'))
		{
			$arrFieldDef['inputType'] = 'text';
			$arrFieldDef['eval']['tl_class'] .= ' wizard';
			$arrFieldDef['wizard'] = array
			(
				array('MetaModels\Dca\AttributeFile', 'filePicker')
			);
		}

		return $arrFieldDef;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function valueToWidget($varValue)
	{
		if (version_compare(VERSION, '3.0', '>='))
		{
			return serialize($varValue['value']);
		}
		else
		{
			return parent::valueToWidget($varValue);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepareTemplate(Template $objTemplate, $arrRowData, $objSettings = null)
	{
		parent::prepareTemplate($objTemplate, $arrRowData, $objSettings);

		$objToolbox = new ToolboxFile();

		$objToolbox->setBaseLanguage($this->getMetaModel()->getActiveLanguage());

		$objToolbox->setFallbackLanguage($this->getMetaModel()->getFallbackLanguage());

		$objToolbox->setLightboxId($this->getMetaModel()->getTableName() . '.' . $arrRowData['id']);

		if (strlen($this->get('file_validFileTypes')))
		{
			$objToolbox->setAcceptedExtensions($this->get('file_validFileTypes'));
		}

		$objToolbox->setShowImages($objSettings->get('file_showImage'));

		if ($objSettings->get('file_imageSize'))
		{
			$objToolbox->setResizeImages($objSettings->get('file_imageSize'));
		}

		if ($arrRowData[$this->getColName()])
		{
			if (is_array($arrRowData[$this->getColName()]))
			{
				foreach ($arrRowData[$this->getColName()] as $strFile)
				{
					if (version_compare(VERSION, '3.0', '<'))
					{
						$objToolbox->addPath($strFile);
					}
					else
					{
						$objToolbox->addPathById($strFile);
					}
				}
			}
			else
			{
				if (version_compare(VERSION, '3.0', '<'))
				{
					$objToolbox->addPath($arrRowData[$this->getColName()]);
				}
				else
				{
					$objToolbox->addPathById($arrRowData[$this->getColName()]);
				}
			}
		}

		$objToolbox->resolveFiles();
		$arrData = $objToolbox->sortFiles($objSettings->get('file_sortBy'));

		$objTemplate->files = $arrData['files'];
		$objTemplate->src   = $arrData['source'];
	}
}
