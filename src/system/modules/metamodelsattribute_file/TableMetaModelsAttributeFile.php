<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package     MetaModels
 * @subpackage  AttributeFile
 * @author      Stefan Heimes <cms@men-at-work.de>
 * @copyright   The MetaModels team.
 * @license     LGPL.
 * @filesource
 */

/**
 * Supplementary class for handling DCA information for select attributes.
 *
 * @package	   MetaModels
 * @subpackage AttributeFile
 * @author     Stefan Heimes <cms@men-at-work.de>
 */
class TableMetaModelsAttributeFile extends Backend
{
	/**
	 * Return the file picker wizard
	 * @param DataContainer
	 * @return string
	 */
	public function filePicker(DataContainer $dc)
	{
		$strField = 'ctrl_' . $dc->inputName . ((Input::getInstance()->get('act') == 'editAll') ? '_' . $dc->id : '');
		return ' ' . $this->generateImage('pickfile.gif', $GLOBALS['TL_LANG']['MSC']['filepicker'], 'style="vertical-align:top;cursor:pointer" onclick="Backend.pickFile(\'' . $strField . '\')"');
	}
}