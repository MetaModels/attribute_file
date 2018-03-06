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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Events\Attribute\File;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Data\DefaultDataProvider;

/**
 * Get the options for the image size.
 */
class ImageSizeOptions
{
    /**
     * Get property options for file image size in the render settings.
     *
     * @param GetPropertyOptionsEvent $event The event.
     *
     * @return void
     */
    public function getPropertyOptions(GetPropertyOptionsEvent $event)
    {
        if (('file_imageSize' !== $event->getPropertyName())
            || ('tl_metamodel_rendersetting' !== $event->getEnvironment()->getDataDefinition()->getName())
        ) {
            return;
        }

        $sizes = $this->getThemeImageSizes();
        if (!$sizes) {
            return;
        }

        $options                = $event->getOptions();
        $options['image_sizes'] = array_replace($sizes, (array) $options['image_sizes']);

        $event->setOptions($options);
    }

    /**
     * Get the image sizes from the theme.
     *
     * @return array
     */
    private function getThemeImageSizes()
    {
        $dataProvider = new DefaultDataProvider();
        $dataProvider->setBaseConfig(array('source' => 'tl_image_size'));

        $config = $dataProvider->getEmptyConfig();
        $config->setFields(array('id', 'name', 'width', 'height'));
        $config->setSorting(array('pid', 'name'));

        $collection = $dataProvider->fetchAll($config);
        if (!$collection->count()) {
            return array();
        }

        $sizes = array();
        foreach ($collection as $model) {
            $sizes[$model->getProperty('id')] = sprintf(
                '%s (%sx%s)',
                $model->getProperty('name'),
                $model->getProperty('width'),
                $model->getProperty('height')
            );
        }

        return $sizes;
    }
}
