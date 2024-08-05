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

namespace MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\RenderSetting;

use Contao\CoreBundle\Image\ImageSizes;
use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use Doctrine\DBAL\Connection;
use MetaModels\CoreBundle\EventListener\DcGeneral\Table\RenderSetting\AbstractListener;
use MetaModels\IFactory;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Add the options for the file image size.
 */
final class FileImageSizeOptions extends AbstractListener
{
    public function __construct(
        RequestScopeDeterminator $scopeDeterminator,
        IFactory $factory,
        Connection $connection,
        private readonly ImageSizes $imageSizes,
        private readonly TranslatorInterface $translator,
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
            ('file_imageSize' !== $event->getPropertyName())
            || (false === $this->wantToHandle($event))
            || (false === $this->isAttributeFile($event))
        ) {
            return;
        }

        $this->addOptions($event);
    }

    /**
     * Add the options.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    private function addOptions(GetPropertyOptionsEvent $event): void
    {
        $options     = $this->imageSizes->getAllOptions();
        $optionsFull = [];
        foreach ($options as $section => $sizeNames) {
            $optionsFull[$this->translateSizeName($section)] = $this->translateSection($section, $sizeNames);
        }

        $event->setOptions($optionsFull);
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

    private function translateSection(string $section, array $sizeNames): array
    {
        if (!\in_array($section, ['relative', 'exact'], true)) {
            return $sizeNames;
        }

        $optionSection = [];
        foreach ($sizeNames as $currentLabel) {
            $optionSection[$currentLabel] = $this->translateSizeName($currentLabel);
        }
        return $optionSection;
    }

    private function translateSizeName(string $sizeName): string
    {
        $key = 'MSC.' . $sizeName . '.0';
        if ($key !== $label = $this->translator->trans($key, [], 'contao_default')) {
            return $label;
        }
        $key = 'MSC.' . $sizeName;
        if ($key !== $label = $this->translator->trans($key, [], 'contao_default')) {
            return $label;
        }

        return $sizeName;
    }
}
