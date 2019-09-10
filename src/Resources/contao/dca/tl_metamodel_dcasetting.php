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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2020 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use Contao\System;
use MetaModels\ContaoFrontendEditingBundle\MetaModelsContaoFrontendEditingBundle;

$GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['attr_id']['file'] = [
    'presentation' => [
        'tl_class'
    ],
    'functions'    => [
        'mandatory',
        'file_widgetMode'
    ]
];

$GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['file_widgetMode'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetMode'],
    'exclude'   => true,
    'inputType' => 'radio',
    'options'   => ['normal'],
    'reference' => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetModes'],
    'eval'      => [
        'default'  => 'normal',
        'chosen'   => true,
        'tl_class' => 'w50 clr'
    ],
    'sql'       => 'char(32) NOT NULL default \'normal\''
];

// Load configuration for the frontend editing.
if (\in_array(MetaModelsContaoFrontendEditingBundle::class, System::getContainer()->getParameter('kernel.bundles'), true)) {
    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['file_widgetMode']['fe_single_upload'] = [
        'upload_settings' => [
            'fe_widget_file_useHomeDir',
            'fe_widget_file_uploadFolder',
            'fe_widget_file_doNotOverwrite',
            'fe_widget_file_extend_folder',
            'fe_widget_file_extend_folder_arguments'
        ]
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['file_widgetMode']['eval']['submitOnChange'] = true;


    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['file_widgetMode']['options'] = \array_merge(
        $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['file_widgetMode']['options'], ['fe_single_upload']
    );

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_useHomeDir'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_useHomeDir'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => [
            'tl_class'  => 'w50',
        ],
        'sql'       => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_uploadFolder'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_uploadFolder'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => [
            'fieldType' =>'radio',
            'tl_class'  =>'w50'
        ],
        'sql'       => "binary(16) NULL"
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_doNotOverwrite'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_doNotOverwrite'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => [
            'tl_class'  => 'w50 m12 clr',
        ],
        'sql'       => "char(1) NOT NULL default ''",
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_extend_folder_arguments'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder_arguments'],
        'exclude'   => true,
        'inputType' => 'multiColumnWizard',
        'eval'      => [
            'tl_class'      => 'w50 clr',
            'columnFields'  => [
                'argument'     => [
                    'label'         => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder_arguments_argument'],
                    'exclude'       => true,
                    'inputType'     => 'select',
                    'eval'          => [
                        'style'              => 'width: 100%;',
                        'includeBlankOption' => true,
                        'chosen'             => true
                    ]
                ]
            ]
        ],
        'sql'       => "blob NULL"
    ];

    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_extend_folder'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder'],
        'inputType' => 'text',
        'eval'      => [
            'maxlength'     => 255,
            'tl_class'      => 'w50 clr'
        ],
        'sql'       => "varchar(255) NOT NULL default ''",
    ];
}
