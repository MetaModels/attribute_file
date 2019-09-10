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
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2020 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetMode'][0] = 'Widget mode';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetMode'][1] =
    'With the mode the display type can be selected.';

$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetModes']['normal']    =
    'Show files as list.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetModes']['downloads'] =
    'Show files as sortable file list e.g. for downloads.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetModes']['gallery']   =
    'Show files as sortable images e.g. for gallery.';

$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['file_widgetModes']['fe_single_upload']      =
    'Single file upload [only for frontend editing]';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_doNotOverwrite'][0]          = 'Preserve existing files';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_doNotOverwrite'][1]          =
    'Add a numeric suffix to the new file, if the file name already exists.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_useHomeDir'][0]              = 'Use home directory';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_useHomeDir'][1]              =
    'If this option is active, store the file in the home directory if there is an authenticated user. ' .
    'If the target folder configured too and the user is authenticated, so this folder is the base upload folder.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_uploadFolder'][0]            = 'Target folder';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_uploadFolder'][1]            =
    'Please select the target folder from the files directory. ' .
    'If the home dir configured too and is not authenticated a user, so this folder is the base folder.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder_arguments'][0] = 'Extend folder arguments';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder_arguments'][1] =
    'Here you can select the arguments to extend the upload folder path with the values.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder'][0]           = 'Extend folder';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder'][1]           =
    'Here you can extend the base upload folder path. If you have configured extend folder options, ' .
    'they will be inserted using the sprintf function. Additionally the Contao inserttags are supported to extend the path.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_deselect'][0]                = 'Deselect file';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_deselect'][1]                =
    'If this option deselect file is active, then that file entry is remove from this model.';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_delete'][0]                  = 'Delete file';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_delete'][1]                  =
    'If this option delete file is active, then that file entry is remove from this model and from the file directory.';

$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['fe_widget_file_extend_folder_arguments_argument'] = ['', ''];

$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['upload_settings_legend'] = 'File upload settings';

$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['argument']['group']['metamodel']  = 'MetaModel';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['argument']['group']['attributes'] = 'Attributes';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['argument']['group']['member']     = 'Member';
$GLOBALS['TL_LANG']['tl_metamodel_dcasetting']['argument']['group']['page']       = 'Page setting';
