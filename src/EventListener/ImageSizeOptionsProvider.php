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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2012-2023 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\EventListener;

use Contao\CoreBundle\Image\ImageSizes;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Get the options for the image size.
 */
class ImageSizeOptionsProvider
{
    public function __construct(
        private readonly ImageSizes $imageSizes,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Add the options.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public function addOptions(GetPropertyOptionsEvent $event): void
    {
        $options     = $this->imageSizes->getAllOptions();
        $optionsFull = [];
        foreach ($options as $section => $sizeNames) {
            $optionsFull[$this->translateSizeName($section)] = $this->translateSection($section, $sizeNames);
        }

        $event->setOptions($optionsFull);
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
