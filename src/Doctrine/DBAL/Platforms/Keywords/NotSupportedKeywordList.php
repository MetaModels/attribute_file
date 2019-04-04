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

namespace MetaModels\AttributeFileBundle\Doctrine\DBAL\Platforms\Keywords;

use Doctrine\DBAL\Platforms\Keywords\KeywordList;

/**
 * This is for platform that has not supported keyword list.
 */
class NotSupportedKeywordList extends KeywordList
{
    /**
     * {@inheritDoc}
     */
    protected function getKeywords(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'NotSupportedKeywordList';
    }

    /**
     * {@inheritDoc}
     */
    public function isKeyword($word): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function initializeKeywords(): void
    {
        // Do nothing
    }
}
