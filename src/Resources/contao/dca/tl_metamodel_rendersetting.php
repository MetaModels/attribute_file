<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2020 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2020 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

/**
 * Table tl_metamodel_attribute
 */

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metapalettes']['file extends default'] = [
    '+advanced' => ['file_sortBy', 'file_showLink', 'file_showImage']
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metasubpalettes']['file_showImage'] = [
    'file_imageSize'
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_sortBy'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_sortBy'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => ['name_asc', 'name_desc', 'date_asc', 'date_desc', 'manual', 'random'],
    'reference' => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting'],
    'sql'       => 'varchar(32) NOT NULL default \'\'',
    'eval'      => [
        'tl_class' => 'w50',
        'chosen'   => true
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_showLink'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_showLink'],
    'inputType' => 'checkbox',
    'sql'       => 'char(1) NOT NULL default \'\'',
    'eval'      => ['tl_class' => 'w50 m12']
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_showImage'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_showImage'],
    'inputType' => 'checkbox',
    'sql'       => 'char(1) NOT NULL default \'\'',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'clr'
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_imageSize'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_imageSize'],
    'exclude'   => true,
    'inputType' => 'imageSize',
    'options'   => $GLOBALS['TL_CROP'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'sql'       => 'varchar(255) NOT NULL default \'\'',
    'eval'      => [
        'rgxp'               => 'digit',
        'includeBlankOption' => true,
        'nospace'            => true,
        'helpwizard'         => true,
        'tl_class'           => 'w50'
    ]
];
