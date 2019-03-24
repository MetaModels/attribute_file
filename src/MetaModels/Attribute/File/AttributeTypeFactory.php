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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Attribute\File;

use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Helper\TableManipulation;

/**
 * Attribute type factory for file attributes.
 */
class AttributeTypeFactory implements IAttributeTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function getTypeName()
    {
        return 'file';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeIcon()
    {
        return 'system/modules/metamodelsattribute_file/html/file.png';
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $metaModel)
    {
        $sortAttribute = $information['colname'] . '__sort';

        $file = new File($metaModel, $information);
        if (empty($information['file_multiple'])
            || $metaModel->hasAttribute($sortAttribute)
        ) {
            return $file;
        }

        try {
            TableManipulation::checkColumnExists($metaModel->getTableName(), $sortAttribute);
        } catch (\Exception $e) {
            return $file;
        }

        // Inject ad-hoc the main attribute before inject the order attribute.
        $metaModel->addAttribute($file);

        return new FileOrder($metaModel, $sortAttribute);
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
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isComplexType()
    {
        return true;
    }
}
