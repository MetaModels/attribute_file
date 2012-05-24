<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * Table tl_metamodel_attribute 
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['file extends _simpleattribute_'] = array
(
	'+advanced' => array('file_showImage', 'file_customFiletree', 'file_multiple'),
	'+backenddisplay'	=> array('-width50'),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_customFiletree'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_customFiletree'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr')
);


$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_multiple'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['multiple'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_sortBy'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['sortBy'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('name_asc', 'name_desc', 'date_asc', 'date_desc', 'meta', 'random'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_metamodel_attribute'],
	'eval'                    => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_showLink'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['showLink'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_showImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_showImage'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true) 
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_imageSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['imageSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'options'                 => array('crop', 'proportional', 'box'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_uploadFolder'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['uploadFolder'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_validFileTypes'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['validFileTypes'],
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_filesOnly'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['filesOnly'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);

?>