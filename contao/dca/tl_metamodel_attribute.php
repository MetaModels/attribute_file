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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['file extends _simpleattribute_'] = array
(
    '+advanced'               => array('file_customFiletree', 'file_multiple'),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metasubpalettes']['file_customFiletree'] = array
(
    'file_uploadFolder', 'file_validFileTypes', 'file_filesOnly'
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_customFiletree'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_customFiletree'],
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange' => true, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_multiple'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_multiple'],
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange' => true, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_uploadFolder'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_uploadFolder'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array('fieldType' => 'radio', 'tl_class' => 'clr')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_validFileTypes'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_validFileTypes'],
    'inputType'               => 'text',
    'eval'                    => array('maxlength' => 255, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_filesOnly'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_filesOnly'],
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class' => 'w50 m12')
);
