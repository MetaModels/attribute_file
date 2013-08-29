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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MetaModels\Attribute\File\File'      => 'system/modules/metamodelsattribute_file/MetaModels/Attribute/File/File.php',
	'MetaModels\Dca\AttributeFile'        => 'system/modules/metamodelsattribute_file/MetaModels/Dca/AttributeFile.php',

	'MetaModelAttributeFile'              => 'system/modules/metamodelsattribute_file/deprecated/MetaModelAttributeFile',
	'TableMetaModelsAttributeFile'        => 'system/modules/metamodelsattribute_file/deprecated/TableMetaModelsAttributeFile',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mm_attr_file'              => 'system/modules/metamodelsattribute_file/templates',
));
