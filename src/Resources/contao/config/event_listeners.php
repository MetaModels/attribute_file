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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use MetaModels\Attribute\File\Subscriber;
use MetaModels\Events\MetaModelsBootEvent;
use MetaModels\MetaModelsEvents;

return [
    MetaModelsEvents::SUBSYSTEM_BOOT_BACKEND => [
        function (MetaModelsBootEvent $event) {
            new Subscriber($event->getServiceContainer());
        }
    ]
];
