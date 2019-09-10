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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeFileBundle\EventListener\DcGeneral\Table\DcaSetting;

use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\BuildWidgetEvent;
use MetaModels\CoreBundle\EventListener\DcGeneral\Table\DcaSetting\AbstractAbstainingListener;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This listener validate the extended folder arguments with extend folder sprintf function.
 */
class ValidateExtendedFolderListener extends AbstractAbstainingListener
{
    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The constructor.
     *
     * @param RequestScopeDeterminator $scopeDeterminator The scope determinator.
     * @param TranslatorInterface      $translator        The translator.
     */
    public function __construct(RequestScopeDeterminator $scopeDeterminator, TranslatorInterface $translator)
    {
        parent::__construct($scopeDeterminator);
        $this->translator = $translator;
    }

    /**
     * Validate the extended folder arguments with extend folder sprintf function.
     *
     * @param BuildWidgetEvent $event The event.
     *
     * @return void.
     */
    public function onValidate(BuildWidgetEvent $event)
    {
        if (!(string) $event->getWidget()->value
            || !$this->wantToHandle($event)
            || !('fe_widget_file_extend_folder' === $event->getProperty()->getName())
            || $event->getWidget()->hasErrors()
            || (false === strpos((string) $event->getWidget()->value, '%'))
        ) {
            return;
        }

        $this->validate($event);
    }

    /**
     * Validate the input of arguments in the format.
     *
     * @param BuildWidgetEvent $event The event.
     *
     * @return void
     */
    private function validate(BuildWidgetEvent $event): void
    {
        $arguments = \array_column(
            $event->getModel()->getProperty('fe_widget_file_extend_folder_arguments'),
            'argument'
        );

        try {
            if (!\count($arguments)
                || !($arguments === \array_filter($arguments))
                || !\sprintf($event->getWidget()->value, ...$arguments)
                || !$this->regularExpressionValidator($event->getWidget()->value, $arguments)
            ) {
                $this->addWidgetError($event);
                return;
            }
        } catch (\Exception $exception) {
            $this->addWidgetError($event);
            return;
        }
    }

    /**
     * Validate the format with regular expression.
     *
     * @param string $format    The format.
     * @param array  $arguments The arguments.
     *
     * @return bool
     */
    private function regularExpressionValidator(string $format, array $arguments): bool
    {
        $pattern = "~%(?:(\d+)[$])?[-+]?(?:[ 0]|['].)?(?:[-]?\d+)?(?:[.]\d+)?[%bcdeEufFgGosxX]~";

        $countArgs = \count($arguments);
        \preg_match_all($pattern, $format, $expected);
        $countVariables = isset($expected[0]) ? \count($expected[0]) : 0;

        return $countArgs === $countVariables;
    }

    /**
     * Add the error message to the widget.
     *
     * @param BuildWidgetEvent $event The event.
     *
     * @return void
     */
    private function addWidgetError(BuildWidgetEvent $event): void
    {
        $event->getWidget()->addError(
            \sprintf(
                $this->translator->trans('ERR.both_input_not_match', [], 'contao_default'),
                $event->getProperty()->getLabel(),
                $event
                    ->getEnvironment()
                    ->getDataDefinition()
                    ->getPropertiesDefinition()
                    ->getProperty('fe_widget_file_extend_folder_arguments')
                    ->getLabel()
            )
        );
    }
}
