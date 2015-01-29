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
 * @subpackage Core
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @copyright  The MetaModels team.
 * @license    LGPL-3+.
 * @filesource
 */

namespace MetaModels\DcGeneral\Events;

use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Image\GenerateHtmlEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ManipulateWidgetEvent;
use MetaModels\IMetaModel;

/**
 * This class adds the file picker wizard to the file picker widgets if necessary.
 *
 * @package MetaModels\DcGeneral\Events
 */
class WizardHandler
{
    /**
     * The MetaModel instance this handler should react on.
     *
     * @var IMetaModel
     */
    protected $metaModel;

    /**
     * The name of the attribute of the MetaModel this handler should react on.
     *
     * @var string
     */
    protected $propertyName;

    /**
     * Create a new instance.
     *
     * @param IMetaModel $metaModel    The MetaModel instance.
     * @param string     $propertyName The name of the property.
     */
    public function __construct($metaModel, $propertyName)
    {
        $this->metaModel    = $metaModel;
        $this->propertyName = $propertyName;
    }

    /**
     * Build the wizard string.
     *
     * @param ManipulateWidgetEvent $event The event.
     *
     * @return void
     */
    public function getWizard(ManipulateWidgetEvent $event)
    {
        if ($event->getModel()->getProviderName() !== $this->metaModel->getTableName()
            || $event->getProperty()->getName() !== $this->propertyName
        ) {
            return;
        }

        $propName   = $event->getProperty()->getName();
        $inputId    = 'ctrl_' . $propName;
        $translator = $event->getEnvironment()->getTranslator();
        if (\Input::get('act') == 'editAll') {
            $inputId .= $event->getModel()->getId();
        }

        /** @var GenerateHtmlEvent $imageEvent */
        $imageEvent = $event->getEnvironment()->getEventDispatcher()->dispatch(
            ContaoEvents::IMAGE_GET_HTML,
            new GenerateHtmlEvent(
                'pickfile.gif',
                $translator->translate('filePicker.0', 'MSC'),
                'style="vertical-align:top"'
            )
        );

        $value = $event->getModel()->getProperty($propName);
        $url   = sprintf(
            'contao/file.php?do=%s&amp;table=%s&amp;field=%s&amp;value=%s&mmfilepicker=1',
            \Input::get('do'),
            $event->getEnvironment()->getDataDefinition()->getName(),
            $inputId,
            $value ? $value : null
        );

        $link = ' <a href="' . $url .
            '" onclick="Backend.getScrollOffset();Backend.openModalSelector({\'width\':765,\'title\':\'' .
            specialchars($translator->translate('files.0', 'MOD')) . '\',\'url\':this.href,\'id\':\'' . $propName .
            '\',\'tag\':\'' . $inputId .
            '\',\'self\':this});return false">%s</a>';

        $event->getWidget()->wizard = sprintf($link, $imageEvent->getHtml());
    }
}
