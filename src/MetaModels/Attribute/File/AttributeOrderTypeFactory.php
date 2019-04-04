<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Attribute\File;

use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Helper\TableManipulation;

/**
 * Attribute type factory for file order attributes.
 */
class AttributeOrderTypeFactory implements IAttributeTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function getTypeName()
    {
        return 'filesort';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeIcon()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $metaModel)
    {
        try {
            TableManipulation::checkColumnExists($metaModel->getTableName(), $information['colname']);
        } catch (\Exception $exception) {
            return null;
        }

        return new FileOrder($metaModel, $information);
    }

    /**
     * {@inheritDoc}
     */
    public function isTranslatedType()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSimpleType()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isComplexType()
    {
        return false;
    }
}
