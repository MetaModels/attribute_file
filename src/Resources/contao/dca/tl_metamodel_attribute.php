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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['file extends _simpleattribute_'] = [
    '+advanced' => ['file_customFiletree', 'file_multiple']
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metasubpalettes']['file_customFiletree'] = [
    'file_uploadFolder',
    'file_validFileTypes',
    'file_filesOnly'
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_customFiletree'] = [
    'label'       => 'file_customFiletree.label',
    'description' => 'file_customFiletree.description',
    'inputType'   => 'checkbox',
    'sql'         => 'char(1) NOT NULL default \'\'',
    'eval'        => ['submitOnChange' => true, 'tl_class' => 'w50']
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_multiple'] = [
    'label'       => 'file_multiple.label',
    'description' => 'file_multiple.description',
    'inputType'   => 'checkbox',
    'sql'         => 'char(1) NOT NULL default \'\'',
    'eval'        => ['tl_class' => 'w50']
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_uploadFolder'] = [
    'label'       => 'file_uploadFolder.label',
    'description' => 'file_uploadFolder.description',
    'exclude'     => true,
    'inputType'   => 'fileTree',
    'sql'         => 'blob NULL',
    'eval'        => ['fieldType' => 'radio', 'tl_class' => 'clr']
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_validFileTypes'] = [
    'label'       => 'file_validFileTypes.label',
    'description' => 'file_validFileTypes.description',
    'inputType'   => 'text',
    'sql'         => 'varchar(255) NOT NULL default \'\'',
    'eval'        => ['maxlength' => 255, 'tl_class' => 'w50']
];

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['file_filesOnly'] = [
    'label'       => 'file_filesOnly.label',
    'description' => 'file_filesOnly.description',
    'inputType'   => 'select',
    'options'     => ['', '1', '2'],
    'reference'   => [
        '' => 'file_filesOnly_options.allow_both',
        '1' => 'file_filesOnly_options.allow_files',
        '2' => 'file_filesOnly_options.allow_folder',
    ],
    'eval'        => ['tl_class' => 'w50'],
    'sql'         => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => '']
];
