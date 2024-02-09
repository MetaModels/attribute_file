<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2023 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2023 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\FilterSetting;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\ContainerInterface;

/**
 * This class provide functions for remove type options, from the filter setting table.
 */
class RemoveAttIdOptions
{
    /**
     * Remove the internal sort attribute from the option list.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public function removeOption(GetPropertyOptionsEvent $event)
    {
        $dataDefinition = $event->getEnvironment()->getDataDefinition();
        assert($dataDefinition instanceof ContainerInterface);
        if (
            ('attr_id' !== $event->getPropertyName())
            || ('tl_metamodel_filtersetting' !== $dataDefinition->getName())
        ) {
            return;
        }

        $options = $event->getOptions() ?? [];
        foreach ($options as $key => $name) {
            $sortKey = $key . '__sort';
            if (\array_key_exists($sortKey, $options) && (\str_ends_with($name, '[file]'))) {
                unset($options[$sortKey]);
            }
        }

        $event->setOptions($options);
    }
}
