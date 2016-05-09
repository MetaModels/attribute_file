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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

/**
 * Table tl_metamodel_attribute
 */

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metapalettes']['file extends default'] = array
(
    '+advanced' => array('file_sortBy', 'file_showLink', 'file_showImage'),
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['metasubpalettes']['file_showImage'] = array
(
    'file_imageSize',
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_sortBy'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_sortBy'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'                 => array('name_asc', 'name_desc', 'date_asc', 'date_desc', 'meta', 'random'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting'],
    'eval'                    => array(
        'tl_class'            => 'w50',
        'chosen'              => true,
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_showLink'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_showLink'],
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class' => 'w50 m12')
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_showImage'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_showImage'],
    'inputType'               => 'checkbox',
    'eval'                    => array(
        'submitOnChange'      => true,
        'tl_class'            => 'clr'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_rendersetting']['fields']['file_imageSize'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_imageSize'],
    'exclude'                 => true,
    'inputType'               => 'imageSize',
    'options'                 => $GLOBALS['TL_CROP'],
    'reference'               => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                    => array(
        'rgxp'                => 'digit',
        'nospace'             => true,
        'helpwizard'          => true,
        'tl_class'            => 'w50'
    )
);
