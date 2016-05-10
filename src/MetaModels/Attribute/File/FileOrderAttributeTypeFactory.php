<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\File;

use MetaModels\Attribute\AbstractAttributeTypeFactory;

/**
 * Attribute type factory for file attributes.
 */
class FileOrderAttributeTypeFactory extends AbstractAttributeTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->typeName  = 'fileOrder';
        $this->typeIcon  = 'system/modules/metamodelsattribute_file/html/file.png';
        $this->typeClass = 'MetaModels\Attribute\File\FileOrder';
    }
}
