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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use Contao\System;
use MetaModels\ContaoFrontendEditingBundle\MetaModelsContaoFrontendEditingBundle;

$GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['attr_id']['file'] = [
    'presentation' => [
        'tl_class',
        'be_template',
    ],
    'functions'    => [
        'mandatory',
        'file_widgetMode'
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['file_widgetMode'] = [
    'label'       => 'file_widgetMode.label',
    'description' => 'file_widgetMode.description',
    'exclude'     => true,
    'inputType'   => 'radio',
    'default'     => 'normal',
    'options'     => ['normal'],
    'reference'   => [
        'normal'                     => 'file_widgetModes.normal',
        'downloads'                  => 'file_widgetModes.downloads',
        'gallery'                    => 'file_widgetModes.gallery',
        'fe_single_upload'           => 'file_widgetModes.fe_single_upload',
        'fe_single_upload_preview'   => 'file_widgetModes.fe_single_upload_preview',
        'fe_multiple_upload'         => 'file_widgetModes.fe_multiple_upload',
        'fe_multiple_upload_preview' => 'file_widgetModes.fe_multiple_upload_preview',
    ],
    'eval'        => [
        'tl_class' => 'clr w50'
    ],
    'sql'         => 'char(32) NOT NULL default \'normal\''
];

// Load configuration for the frontend editing.
if (\in_array(
    MetaModelsContaoFrontendEditingBundle::class,
    System::getContainer()->getParameter('kernel.bundles'),
    true
)) {
    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['attr_id']['file']['presentation'][] =
        'fe_template';

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['file_widgetMode']['eval']['submitOnChange'] = true;

    $uploadSettings = [
        'upload_settings' => [
            'fe_widget_file_useHomeDir',
            'fe_widget_file_uploadFolder',
            'fe_widget_file_extend_folder',
            'fe_widget_file_normalize_extend_folder',
            'fe_widget_file_doNotOverwrite',
            'fe_widget_file_normalize_filename',
            'fe_widget_file_prefix_filename',
            'fe_widget_file_postfix_filename',
            'fe_widget_file_deselect',
            'fe_widget_file_delete'
        ]
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['file_widgetMode']['fe_single_upload'] =
        $uploadSettings;

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['file_widgetMode']['fe_single_upload_preview'] =
        \array_merge_recursive(
            $uploadSettings,
            ['upload_settings' => ['fe_widget_file_imageSize']]
        );

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['file_widgetMode']['fe_multiple_upload'] =
        \array_merge_recursive(
            $uploadSettings,
            ['upload_settings' => ['fe_widget_file_sortBy']]
        );

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['file_widgetMode']['fe_multiple_upload_preview'] =
        \array_merge_recursive(
            $uploadSettings,
            ['upload_settings' => ['fe_widget_file_sortBy', 'fe_widget_file_imageSize']]
        );

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_useHomeDir'] = [
        'label'       => 'fe_widget_file_useHomeDir.label',
        'description' => 'fe_widget_file_useHomeDir.description',
        'exclude'     => true,
        'inputType'   => 'checkbox',
        'eval'        => [
            'tl_class' => 'w50 cbx m12',
        ],
        'sql'         => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_uploadFolder'] = [
        'label'       => 'fe_widget_file_uploadFolder.label',
        'description' => 'fe_widget_file_uploadFolder.description',
        'exclude'     => true,
        'inputType'   => 'fileTree',
        'eval'        => [
            'fieldType' => 'radio',
            'tl_class'  => 'w50'
        ],
        'sql'         => "binary(16) NULL"
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_doNotOverwrite'] = [
        'label'       => 'fe_widget_file_doNotOverwrite.label',
        'description' => 'fe_widget_file_doNotOverwrite.description',
        'exclude'     => true,
        'inputType'   => 'checkbox',
        'eval'        => [
            'tl_class' => 'w50 cbx m12 clr',
        ],
        'sql'         => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_deselect'] = [
        'label'       => 'fe_widget_file_deselect.label',
        'description' => 'fe_widget_file_deselect.description',
        'exclude'     => true,
        'inputType'   => 'checkbox',
        'eval'        => [
            'tl_class' => 'w50 clr cbx m12',
        ],
        'sql'         => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_delete'] = [
        'label'       => 'fe_widget_file_delete.label',
        'description' => 'fe_widget_file_delete.description',
        'exclude'     => true,
        'inputType'   => 'checkbox',
        'eval'        => [
            'tl_class' => 'w50 cbx m12',
        ],
        'sql'         => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_extend_folder'] = [
        'label'       => 'fe_widget_file_extend_folder.label',
        'description' => 'fe_widget_file_extend_folder.description',
        'inputType'   => 'text',
        'eval'        => [
            'tl_class' => 'w50 clr'
        ],
        'sql'         => "longtext"
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_normalize_extend_folder'] = [
        'label'       => 'fe_widget_file_normalize_extend_folder.label',
        'description' => 'fe_widget_file_normalize_extend_folder.description',
        'exclude'     => true,
        'inputType'   => 'checkbox',
        'eval'        => [
            'tl_class' => 'w50 cbx m12',
        ],
        'sql'         => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_normalize_filename'] = [
        'label'       => 'fe_widget_file_normalize_filename.label',
        'description' => 'fe_widget_file_normalize_filename.description',
        'exclude'     => true,
        'inputType'   => 'checkbox',
        'eval'        => [
            'tl_class' => 'w50 cbx m12',
        ],
        'sql'         => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_prefix_filename'] = [
        'label'       => 'fe_widget_file_prefix_filename.label',
        'description' => 'fe_widget_file_prefix_filename.description',
        'inputType'   => 'text',
        'eval'        => [
            'tl_class' => 'w50 clr'
        ],
        'sql'         => "longtext"
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_postfix_filename'] = [
        'label'       => 'fe_widget_file_postfix_filename.label',
        'description' => 'fe_widget_file_postfix_filename.description',
        'inputType'   => 'text',
        'eval'        => [
            'tl_class' => 'w50'
        ],
        'sql'         => "longtext"
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_sortBy'] = [
        'label'       => 'fe_widget_file_sortBy.label',
        'description' => 'fe_widget_file_sortBy.description',
        'exclude'     => true,
        'inputType'   => 'select',
        'options'     => ['name_asc', 'name_desc', 'date_asc', 'date_desc', 'random'],
        'reference'   => [
            'name_asc'  => 'fe_widget_file_sortBy.name_asc',
            'name_desc' => 'fe_widget_file_sortBy.name_desc',
            'date_asc'  => 'fe_widget_file_sortBy.date_asc',
            'date_desc' => 'fe_widget_file_sortBy.date_desc',
            'random'    => 'fe_widget_file_sortBy.random',
        ],
        'sql'         => 'varchar(32) NOT NULL default \'\'',
        'eval'        => [
            'tl_class' => 'w50',
            'chosen'   => true
        ]
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_imageSize'] = [
        'label'       => 'fe_widget_file_imageSize.label',
        'description' => 'fe_widget_file_imageSize.description',
        'exclude'     => true,
        'inputType'   => 'imageSize',
        'options_callback' => static function () {
            return System::getContainer()->get('contao.image.sizes')?->getOptionsForUser(BackendUser::getInstance());
        },
        'reference'   => &$GLOBALS['TL_LANG']['MSC'],
        'sql'         => 'varchar(255) NOT NULL default \'\'',
        'eval'        => [
            'rgxp'               => 'digit',
            'includeBlankOption' => true,
            'nospace'            => true,
            'helpwizard'         => true,
            'tl_class'           => 'w50'
        ]
    ];
}
