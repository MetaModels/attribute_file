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
 * @author     Stefan Heimes <cms@men-at-work.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Dca;

use DcGeneral\DataContainerInterface;

/**
 * Supplementary class for handling DCA information for select attributes.
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Stefan Heimes <cms@men-at-work.de>
 */
class AttributeFile extends \Backend
{
	/**
	 * Return the file picker wizard
	 *
	 * @param DataContainerInterface
	 *
	 * @return string
	 */
	public function filePicker(DataContainerInterface $dc)
	{
		// FIXME: inputName is not available in DC_General, we need a substitute.
		$strField = 'ctrl_' . $dc->inputName . ((\Input::getInstance()->get('act') == 'editAll') ? '_' . $dc->id : '');
		return ' ' . $this->generateImage('pickfile.gif', $GLOBALS['TL_LANG']['MSC']['filepicker'], 'style="vertical-align:top;cursor:pointer" onclick="Backend.pickFile(\'' . $strField . '\')"');
	}
}
