<?php

/**
 * This file is part of MetaModels/core.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/core
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/core/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\EventListener;

use MetaModels\Attribute\Events\CollectMetaModelAttributeInformationEvent;

/**
 * This class add attribute information.
 */
class AddAttributeInformation
{
    /**
     * Add the information.
     *
     * @param CollectMetaModelAttributeInformationEvent $event The event.
     *
     * @return void
     */
    public function addInformation(CollectMetaModelAttributeInformationEvent $event)
    {
        if (!\count($information = $event->getAttributeInformation())) {
            return;
        }

        if (!\count($fileInformation = $this->collectFileInformation($information))) {
            return;
        }

        $event->setAttributeInformation($this->updateInformation($fileInformation, $information));
    }

    /**
     * Collect the file information.
     *
     * @param array $information The information search for file informtation.
     *
     * @return array
     */
    private function collectFileInformation(array $information)
    {
        return \array_filter(
            $information,
            function ($attributeInformation) {
                return (('file' === $attributeInformation['type'])
                        && \array_key_exists('file_multiple', $attributeInformation)
                        && $attributeInformation['file_multiple']);
            }
        );
    }

    /**
     * Update the information.
     *
     * @param array $inputInformation  The input information, who add to the update information.
     * @param array $updateInformation The update information, who becomes update from the input information.
     *
     * @return array
     */
    private function updateInformation(array $inputInformation, array $updateInformation)
    {
        foreach ($inputInformation as $name => $information) {
            $columnName = $information['colname'] . '__sort';
            $position   = \array_flip(\array_keys($updateInformation))[$name];

            $updateInformation = \array_merge(
                \array_slice($updateInformation, 0, ($position + 1)),
                [
                    $columnName => [
                        'colname' => $columnName,
                        'type'    => 'filesort'
                    ]
                ],
                \array_slice($updateInformation, ($position ? $position - 1 : $position + 1))
            );
        }

        return $updateInformation;
    }
}
