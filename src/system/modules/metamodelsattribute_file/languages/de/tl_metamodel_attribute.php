<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package	   MetaModels
 * @subpackage Backend
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */
if (!defined('TL_ROOT'))
{
	die('You cannot access this file directly!');
}

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['typeOptions']['file']        = 'Datei';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_customFiletree'] = array('Dateibaum anpassen', 'Mit dieser Option können Sie individuelle Optionen für die Einbindung von Dateien erstellen.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_uploadFolder']   = array('Wurzelverzeichnis', 'Wählen Sie das Verzeichnis aus, das Nutzern als Wurzelverzeichnis im Dateibaum dienen soll, wenn sie in der Dateiauswahl eine Auswahl treffen.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_validFileTypes'] = array('Gültige Dateitypen', 'Bitte geben Sie eine kommagetrennte Liste der Dateitypen ein, die für dieses Feld gültig sind.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_filesOnly']      = array('Nur Dateien erlauben', 'Wählen Sie diese Option aus, damit die Auswahl auf Dateien beschränkt bleibt. Ordner werden nicht auswählbar sein.');

$GLOBALS['TL_LANG']['tl_metamodel_attribute']['file_multiple']       = array('Mehrfachauswahl', 'Wenn Sie die Mehrfachauswahl aktivieren können Benutzer mehrere Dateien (oder Ordner, falls freigegeben) auswählen.');


?>