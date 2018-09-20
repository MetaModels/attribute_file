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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\EventListener;

use MetaModels\Factory;

/**
 * Class BaseListener
 *
 * @package MetaModels\AttributeFileBundle\Events
 */
class BaseListener
{
    /**
     * @var Factory|null
     */
    private $factory = null;

    /**
     * Attribute constructor.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Return the factory of MetaModels.
     *
     * @return Factory|null
     */
    protected function getFactory()
    {
        return $this->factory;
    }
}
