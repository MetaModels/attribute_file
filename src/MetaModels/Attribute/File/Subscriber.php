<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage AttributeFile
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\File;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelInterface;
use MetaModels\DcGeneral\Events\BaseSubscriber;
use MetaModels\IMetaModel;

/**
 * Subscriber integrates file attribute related listeners.
 *
 * @package MetaModels\Attribute\File
 */
class Subscriber extends BaseSubscriber
{
    /**
     * The meta models cache.
     *
     * @var array
     */
    private $metaModelCache = array();

    /**
     * {@inheritdoc}
     */
    public function registerEventsInDispatcher()
    {
        $this->addListener(
            GetPropertyOptionsEvent::NAME,
            array($this, 'getFileOrderAttributes')
        );
    }

    /**
     * Get file order attributes.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @void
     */
    public function getFileOrderAttributes(GetPropertyOptionsEvent $event)
    {
        if (($event->getEnvironment()->getDataDefinition()->getName() !== 'tl_metamodel_attribute')
            || ($event->getPropertyName() !== 'file_orderField')) {
            return;
        }

        $database  = $this->getDatabase();
        $model     = $event->getModel();
        $metaModel = $this->getMetaModel($model);

        if (!$metaModel) {
            return;
        }

        $options = array();

        // Fetch all attributes that exist in other settings.
        $alreadyTaken = $database
            ->prepare('
            SELECT
                file_orderField
            FROM
                ' . $model->getProviderName() . '
            WHERE
                type=?
                AND id<>?
                AND pid=?')
            ->execute(
                $model->getProperty('type'),
                $model->getProperty('attr_id'),
                $model->getProperty('pid')
            )
            ->fetchEach('attr_id');

        foreach ($metaModel->getAttributes() as $attribute) {
            if ($attribute->get('type') !== 'fileOrder' || in_array($attribute->get('id'), $alreadyTaken)) {
                continue;
            }
            $options[$attribute->get('id')] = sprintf(
                '%s [%s]',
                $attribute->getName(),
                $attribute->get('type')
            );
        }

        $event->setOptions($options);
    }


    /**
     * Retrieve the MetaModel instance from a render settings model.
     *
     * @param ModelInterface $model The model to fetch the MetaModel instance for.
     *
     * @return IMetaModel
     */
    protected function getMetaModel($model)
    {
        if (!isset($this->metaModelCache[$model->getProperty('pid')])) {
            $dbResult = $this
                ->getDatabase()
                ->prepare('SELECT * FROM tl_metamodel_rendersettings WHERE id=?')
                ->execute($model->getProperty('pid'))
                ->row();

            $this->metaModelCache[$model->getProperty('pid')] = $this->getMetaModelById($dbResult['pid']);
        }

        return $this->metaModelCache[$model->getProperty('pid')];
    }
}
