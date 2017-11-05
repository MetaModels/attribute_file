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

$GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['attr_id']['file'] = array
(
    'presentation' => array(
        'tl_class',
    ),
    'functions'  => array(
        'mandatory',
    )
);

if (in_array('metamodels-contao-frontend-editing', \Contao\ModuleLoader::getActive())) {
    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['palettes']['__selector__'][] = 'fe_widget';

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['attr_id']['file']['functions'][] =
        'fe_widget';

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget'],
        'exclude'   => true,
        'inputType' => 'select',
        'eval'      => [
            'tl_class'           => 'w50',
            'mandatory'          => true,
            'submitOnChange'     => true,
            'includeBlankOption' => true,
        ],
        'sql'       => "varchar(64) NOT NULL default ''",
    ];
    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['doNotOverwrite'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['doNotOverwrite'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => [
            'tl_class'           => 'w50 m12',
        ],
        'sql'       => "char(1) NOT NULL default ''",
    ];
    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['useHomeDir'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['useHomeDir'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => [
            'tl_class'           => 'w50 m12',
        ],
        'sql'       => "char(1) NOT NULL default ''",
    ];
    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fallbackImage'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fallbackImage'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => [
            'fieldType'  => 'radio',
            'files'      => true,
            'filesOnly'  => true,
            'extensions' => $GLOBALS['TL_CONFIG']['validImageTypes'],
            'tl_class'   => 'w50 w50h',
        ],
        'sql'       => "binary(16) NULL",
    ];
}
