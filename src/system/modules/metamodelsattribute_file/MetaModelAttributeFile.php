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

/**
 * This is the MetaModelAttribute class for handling file fields.
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class MetaModelAttributeFile extends MetaModelAttributeSimple
{
	/////////////////////////////////////////////////////////
	// interface IMetaModelAttribute
	/////////////////////////////////////////////////////////

	/**
	 * {@inheritdoc}
	 */
	public function getSQLDataType()
	{
		return 'text NULL';
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
	 * {@inheritdoc}
	 */
	public function getFieldDefinition($arrOverrides = array())
	{
		$arrFieldDef=parent::getFieldDefinition($arrOverrides);

		$arrFieldDef['inputType']          = 'fileTree';
		$arrFieldDef['eval']['files']      = true;
		$arrFieldDef['eval']['fieldType']  = $this->get('file_multiple') ? 'checkbox' : 'radio';
		$arrFieldDef['eval']['extensions'] = $GLOBALS['TL_CONFIG']['allowedDownload'];

		if ($this->get('file_customFiletree'))
		{
			if (strlen($this->get('file_uploadFolder')))
			{
				$arrFieldDef['eval']['path'] = $this->get('file_uploadFolder');
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
		if($this->get('file_filePicker'))
		{
			$arrFieldDef['inputType'] = 'text';
			$arrFieldDef['eval']['tl_class'] .= ' wizard';
			$arrFieldDef['wizard'] = array
			(
				array('TableMetaModelsAttributeFile', 'filePicker')
			);
		}
		
		return $arrFieldDef;
	}

	/**
	 * {@inheritdoc}
	 *
	 */
	protected function prepareTemplate(MetaModelTemplate $objTemplate, $arrRowData, $objSettings = null)
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
					$objToolbox->addPath($strFile);
				}
			}
			else
			{
				$objToolbox->addPath($arrRowData[$this->getColName()]);
			}
		}

		$objToolbox->resolveFiles();
		$arrData = $objToolbox->sortFiles($objSettings->get('file_sortBy'));

		$objTemplate->files = $arrData['files'];
		$objTemplate->src   = $arrData['source'];
	}
}
