<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2024 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\DcaSetting;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use Doctrine\DBAL\Connection;
use MetaModels\AttributeFileBundle\EventListener\ImageSizeOptionsProvider;
use MetaModels\CoreBundle\EventListener\DcGeneral\Table\DcaSetting\AbstractListener;
use MetaModels\IFactory;

/**
 * Add the options for the FEE file image size.
 */
final class FeeFileImageSizeOptions extends AbstractListener
{
    public function __construct(
        RequestScopeDeterminator $scopeDeterminator,
        IFactory $factory,
        Connection $connection,
        private readonly ImageSizeOptionsProvider $optionsProvider,
    ) {
        parent::__construct($scopeDeterminator, $factory, $connection);
    }

    /**
     * Invoke the event.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public function __invoke(GetPropertyOptionsEvent $event): void
    {
        if (
            ('fe_widget_file_imageSize' !== $event->getPropertyName())
            || (false === $this->wantToHandle($event))
            || (false === $this->isAttributeFile($event))
        ) {
            return;
        }

        $this->optionsProvider->addOptions($event);
    }

    /**
     * If used attribute type of file.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return bool
     */
    private function isAttributeFile(GetPropertyOptionsEvent $event): bool
    {
        $builder = $this->connection->createQueryBuilder();
        $builder
            ->select('t.type')
            ->from('tl_metamodel_attribute', 't')
            ->where($builder->expr()->eq('t.id', ':id'))
            ->setParameter('id', $event->getModel()->getProperty('attr_id'));

        $statement = $builder->executeQuery();
        if (0 === $statement->columnCount()) {
            return false;
        }

        $result = $statement->fetchAssociative();

        return 'file' === ($result['type'] ?? null);
    }
}
