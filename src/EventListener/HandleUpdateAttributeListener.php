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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\EventListener;

use ContaoCommunityAlliance\DcGeneral\Event\PostPersistModelEvent;
use Doctrine\DBAL\Connection;
use MetaModels\Factory;
use MetaModels\Helper\TableManipulation;

/**
 * Class HandleUpdateAttributeListener
 */
class HandleUpdateAttributeListener extends BaseListener
{
    /**
     * The doctrine dbal connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * HandleUpdateAttributeListener constructor.
     *
     * @param Factory    $factory    The attribute factory.
     * @param Connection $connection The doctrine dbal connection.
     */
    public function __construct(Factory $factory, Connection $connection)
    {
        parent::__construct($factory);

        $this->connection = $connection;
    }

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

        if (('file' !== $model->getProperty('type'))
            || (!$model->getProperty('file_multiple'))
            || ('tl_metamodel_attribute' !== $event->getEnvironment()->getDataDefinition()->getName())
        ) {
            return;
        }

        $metaModelsName = $this->getFactory()->translateIdToMetaModelName($model->getProperty('pid'));
        $metaModel      = $this->getFactory()->getMetaModel($metaModelsName);
        $attributeName  = $model->getProperty('colname') . '__sort';
        $tableColumns   = $this->connection->getSchemaManager()->listTableColumns($metaModel->getTableName());

        if (\array_key_exists($attributeName, $tableColumns)) {
            return;
        }

        TableManipulation::createColumn($metaModel->getTableName(), $attributeName, 'blob NULL');
    }
}
