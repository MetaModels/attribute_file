<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2018 The MetaModels team.
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
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\DcGeneral;

use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\DefinitionInterface;

/**
 * This definition holds the mapping between file attributes and a property.
 */
class AttributeFileDefinition implements DefinitionInterface
{
    /**
     * The buffered properties.
     *
     * @var string[]
     */
    private $fileProperties = array();

    /**
     * Add a file property.
     *
     * @param string $filePropertyName The name of the file property.
     *
     * @return void
     */
    public function add($filePropertyName)
    {
        if (\in_array($filePropertyName, $this->fileProperties)) {
            return;
        }
        $this->fileProperties[] = $filePropertyName;
    }

    /**
     * Retrieve the names of the file properties.
     *
     * @return string[]
     */
    public function get()
    {
        return $this->fileProperties;
    }
}
