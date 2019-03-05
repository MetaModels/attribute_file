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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\EventListener;

use ContaoCommunityAlliance\DcGeneral\Event\PostPersistModelEvent;
use MetaModels\Helper\TableManipulation;

/**
 * Class HandleUpdateAttributeListener
 */
class HandleUpdateAttributeListener extends BaseListener
{
    /**
     * Handle the update of the file attribute, if switch on for file multiple.
     *
     * @param PostPersistModelEvent $event The event.
     *
     * @return void
     *
     * @throws \Exception If column not exist in the table.
     */
    public function handleUpdateAttribute(PostPersistModelEvent $event)
    {
        $model = $event->getModel();

        if (($model->getProperty('type') !== 'file')
            || (!$model->getProperty('file_multiple'))
            || ($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
        ) {
            return;
        }

        $metaModelsName = $this->getFactory()->translateIdToMetaModelName($model->getProperty('pid'));
        $metaModel      = $this->getFactory()->getMetaModel($metaModelsName);
        $attributeName  = $model->getProperty('colname') . '__sort';

        try {
            TableManipulation::checkColumnExists($metaModel->getTableName(), $attributeName);
        } catch (\Exception $e) {
            TableManipulation::createColumn($metaModel->getTableName(), $attributeName, 'blob NULL');
        }
    }
}
