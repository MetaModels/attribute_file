<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @author     Cliff Parnitzky <github@cliff-parnitzky.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use Contao\BackendUser;
use Contao\System;

/**
 * Table tl_metamodel_rendersettings
 */

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metapalettes']['file extends default'] = [
    '+advanced' => ['file_sortBy', 'file_showLink', 'file_showImage']
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metasubpalettes']['file_showLink'] = [
    'file_protectedDownload'
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metasubpalettes']['file_showImage'] = [
    'file_imageSize',
    'file_placeholder'
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_sortBy'] = [
    'label'       => 'file_sortBy.label',
    'description' => 'file_sortBy.description',
    'exclude'     => true,
    'inputType'   => 'select',
    'options'     => ['name_asc', 'name_desc', 'date_asc', 'date_desc', 'manual', 'random'],
    'reference'   => [
        'name_asc'  => 'file_sortBy.name_asc',
        'name_desc' => 'file_sortBy.name_desc',
        'date_asc'  => 'file_sortBy.date_asc',
        'date_desc' => 'file_sortBy.date_desc',
        'random'    => 'file_sortBy.random',
        'manual'    => 'file_sortBy.manual',
    ],
    'sql'         => 'varchar(32) NOT NULL default \'\'',
    'eval'        => [
        'tl_class' => 'w50',
        'chosen'   => true
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_showLink'] = [
    'label'       => 'file_showLink.label',
    'description' => 'file_showLink.description',
    'inputType'   => 'checkbox',
    'sql'         => 'char(1) NOT NULL default \'\'',
    'eval'        => [
        'submitOnChange' => true,
        'tl_class'       => 'clr w50 cbx m12'
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_protectedDownload'] = [
    'label'       => 'file_protectedDownload.label',
    'description' => 'file_protectedDownload.description',
    'inputType'   => 'checkbox',
    'sql'         => 'char(1) NOT NULL default \'\'',
    'eval'        => [
        'tl_class' => 'w50 cbx m12'
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_showImage'] = [
    'label'       => 'file_showImage.label',
    'description' => 'file_showImage.description',
    'inputType'   => 'checkbox',
    'sql'         => 'char(1) NOT NULL default \'\'',
    'eval'        => [
        'submitOnChange' => true,
        'tl_class'       => 'clr w50 cbx m12'
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_imageSize'] = [
    'label'            => 'file_imageSize.label',
    'description'      => 'file_imageSize.description',
    'exclude'          => true,
    'inputType'        => 'imageSize',
    'eval'             => [
        'rgxp'               => 'natural',
        'includeBlankOption' => true,
        'nospace'            => true,
        'tl_class'           => 'clr w50'
    ],
    'sql'              => 'varchar(128) COLLATE ascii_bin NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_placeholder'] = [
    'label'       => 'file_placeholder.label',
    'description' => 'file_placeholder.description',
    'exclude'     => true,
    'inputType'   => 'fileTree',
    'sql'         => 'blob NULL',
    'eval'        => [
        'fieldType' => 'radio',
        'files'     => true,
        'filesOnly' => true,
        'tl_class'  => 'w50'
    ]
];
