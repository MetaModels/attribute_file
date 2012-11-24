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
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_showImage'] = array('Vorschaubild aktivieren', 'Falls gewählt wird ein Vorschaubild (Thumbnail) erzeugt.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_sortBy']    = array('Sortieren nach ...', 'Bitte den Sortiermodus auswählen.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_showLink']  = array('Link als Download oder Lightbox erzeugen', 'Mit Aktivierung dieser Option wird das Bild mit einem Link umgeben, um entweder einen Download oder eine Lightbox zu ermöglichen.');
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['file_imageSize'] = array('Bildbreite und -höhe', 'Bitte die entweder die Bildbreite, die Bildhöhe oder beides angeben, damit die Bildgröße angepasst wird. Falls dieses feld leer bleibt wird das Bild in Originalgröße angezeigt.');

$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['name_asc']  = 'Dateiname (aufsteigend)';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['name_desc'] = 'Dateiname (absteigend)';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['date_asc']  = 'Datum (absteigend)';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['date_desc'] = 'Datum (aufsteigend)';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['meta']      = 'Meta-Datei (meta.txt)';
$GLOBALS['TL_LANG']['tl_metamodel_rendersetting']['random']    = 'Zufällige Reihenfolge';


?>