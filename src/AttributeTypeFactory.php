<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2017 The MetaModels team.
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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\File;

use Doctrine\DBAL\Driver\Connection;
use MetaModels\Attribute\AbstractSimpleAttributeTypeFactory;
use MetaModels\Helper\TableManipulator;

/**
 * Attribute type factory for file attributes.
 */
class AttributeTypeFactory extends AbstractSimpleAttributeTypeFactory
{
    /**
     * {@inheritDoc}
     *
     * @param Connection       $connection       The database connection.
     * @param TableManipulator $tableManipulator The table manipulator.
     */
    public function __construct(Connection $connection, TableManipulator $tableManipulator)
    {
        parent::__construct($connection, $tableManipulator);

        $this->typeName  = 'file';
        $this->typeIcon  = 'bundles/metamodelsattributefilebundle/file.png';
        $this->typeClass = 'MetaModels\Attribute\File\File';
    }
}
