<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

// Fields.
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['typeOptions']['file']    = 'File';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_customFiletree'][0] = 'Customize the file tree';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_customFiletree'][1] = 'Allows you to set custom options for the filetree.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_uploadFolder'][0]   = 'Set file root folder';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_uploadFolder'][1]   = 'Selects the root point from which the user will select this file field.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_validFileTypes'][0] = 'Valid file types';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_validFileTypes'][1] = 'To overwrite the contao standard file types, please enter a comma separated list of extensions of valid file types for this field.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_filesOnly'][0]      = 'Allow files only';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_filesOnly'][1]      = 'Select this option to restrict the file browser to files only (folders not selectable).';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_multiple'][0]       = 'Multiple selection';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_multiple'][1]       = 'If selected, user will be able to select more than one item.';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_filePicker'][0]     = 'Use file picker';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_filePicker'][1]     = 'If selected, use the file picker instead of the filetree.';
