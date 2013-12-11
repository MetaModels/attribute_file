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
		if(version_compare(VERSION,'3.1', '>=')){
			$currentField = $dc->getEnvironment()->getCurrentModel()->getItem()->get($dc->field);
			return ' <a href="' . \Environment::getInstance()->base . 'contao/file.php?do='.\Input::get('do').'&amp;table='.$dc->table.'&amp;field='.$dc->field . '_' . $dc->id . '&amp;value='.$currentField['path'][0].'" title="'.specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['MSC']['filepicker'])).'" onclick="Backend.getScrollOffset();Backend.openModalSelector({\'width\':765,\'title\':\''.specialchars($GLOBALS['TL_LANG']['MOD']['files'][0]).'\',\'url\':this.href,\'id\':\''.$dc->field.'\',\'tag\':\'ctrl_'.$dc->field . '_' . $dc->id . ((\Input::get('act') == 'editAll') ? '_' . $dc->id : '').'\',\'self\':this});return false">' . \Image::getHtml('pickfile.gif', $GLOBALS['TL_LANG']['MSC']['filepicker'], 'style="vertical-align:top;cursor:pointer"') . '</a>';
		}

		// FIXME: inputName is not available in DC_General, we need a substitute.
		$strField = 'ctrl_' . $dc->inputName . ((\Input::getInstance()->get('act') == 'editAll') ? '_' . $dc->id : '');
		return ' ' . $this->generateImage('pickfile.gif', $GLOBALS['TL_LANG']['MSC']['filepicker'], 'style="vertical-align:top;cursor:pointer" onclick="Backend.pickFile(\'' . $strField . '\')"');
	}
}
