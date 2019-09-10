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

use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminator;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Connection;
use MenAtWork\MultiColumnWizardBundle\Event\GetOptionsEvent;
use MetaModels\CoreBundle\EventListener\DcGeneral\Table\DcaSetting\AbstractListener;
use MetaModels\IFactory;
use MetaModels\IMetaModel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This listener add the options for the column field argument.
 *
 * @see $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['fields']['fe_widget_file_extend_folder_arguments']
 */
final class AddArgumentOptionsListener extends AbstractListener
{
    /**
     * The scope determinator.
     *
     * @var RequestScopeDeterminator
     */
    private $scopeDeterminator;

    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The cache.
     *
     * @var Cache
     */
    private $cache;

    /**
     * The token storage.
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Adapter to the Contao\Controller class.
     *
     * @var Controller
     */
    private $controller;

    /**
     * Create a new instance.
     *
     * @param RequestScopeDeterminator $scopeDeterminator The scope determinator.
     * @param IFactory                 $factory           The MetaModel factory.
     * @param Connection               $connection        The database connection.
     * @param TranslatorInterface      $translator        The translator.
     * @param Cache                    $cache             The cache.
     * @param TokenStorageInterface    $tokenStorage      The token storage.
     * @param Adapter                  $controllerAdapter The controller adapter to load languages and data containers.
     */
    public function __construct(
        RequestScopeDeterminator $scopeDeterminator,
        IFactory $factory,
        Connection $connection,
        TranslatorInterface $translator,
        Cache $cache,
        TokenStorageInterface $tokenStorage,
        Adapter $controllerAdapter
    ) {
        parent::__construct($scopeDeterminator, $factory, $connection);
        $this->scopeDeterminator = $scopeDeterminator;
        $this->translator        = $translator;
        $this->cache             = $cache;
        $this->tokenStorage      = $tokenStorage;
        $this->controller        = $controllerAdapter;
    }

    /**
     * Get the options for the column field argument.
     *
     * @param GetOptionsEvent $event The event.
     *
     * @return void
     */
    public function onGetOptions(GetOptionsEvent $event): void
    {
        if (!$this->wantToHandleGetOptions($event)
            || !('fe_widget_file_extend_folder_arguments' === $event->getPropertyName())
            || !('argument' === $event->getSubPropertyName())
        ) {
            return;
        }

        /** @var BackendUser $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $cacheKey = \sprintf(
            '%s_%s_%s_%s',
            $event->getModel()->getProviderName(),
            $event->getPropertyName(),
            $event->getSubPropertyName(),
            $user->language
        );

        $options = [];
        if ($this->cache->contains($cacheKey)) {
            $options = $this->cache->fetch($cacheKey);
        }

        if (!\count($options)) {
            $options = $this->collectOptions($this->getMetaModelFromModel($event->getModel()));

            $this->cache->save($cacheKey, $options);
        }

        $event->setOptions($options);
    }

    /**
     * Collect the options.
     *
     * @param IMetaModel $metaModel The meta model.
     *
     * @return array
     */
    private function collectOptions(IMetaModel $metaModel): array
    {
        $options = [[]];

        $options[] = $this->collectMetaModelOption($metaModel);
        $options[] = $this->collectAttributeOptions($metaModel);
        $options[] = $this->collectMemberOptions();
        $options[] = $this->collectPageOptions();

        return \array_merge(...$options);
    }

    /**
     * Collect the options for the meta model group.
     *
     * @param IMetaModel $metaModel The meta model.
     *
     * @return array
     */
    private function collectMetaModelOption(IMetaModel $metaModel): array
    {
        $group = \sprintf(
            '%s [%s - %s]',
            $this->translator
                ->trans('tl_metamodel_dcasetting.argument.group.metamodel', [], 'contao_tl_metamodel_dcasetting'),
            $metaModel->getName(),
            $metaModel->getTableName()
        );

        $values = [
            'tl_metamodel.name'      => 'name',
            'tl_metamodel.tableName' => 'tableName',
            'tl_metamodel.id'        => 'id',
            'tl_metamodel.sorting'   => 'sorting',
            'tl_metamodel.tstamp'    => 'tstamp',
            'tl_metamodel.mode'      => 'mode'
        ];

        return [$group => $values];
    }

    /**
     * Collect the options for the attribute group.
     *
     * @param IMetaModel $metaModel The meta model.
     *
     * @return array
     */
    private function collectAttributeOptions(IMetaModel $metaModel): array
    {
        $group = \sprintf(
            '%s [%s - %s]',
            $this->translator
                ->trans('tl_metamodel_dcasetting.argument.group.attributes', [], 'contao_tl_metamodel_dcasetting'),
            $metaModel->getName(),
            $metaModel->getTableName()
        );

        $values = [];
        foreach ($metaModel->getAttributes() as $attribute) {
            $values['tl_metamodel_attribute.' . $attribute->getColName()] = \sprintf(
                '%s [%s - %s]',
                $attribute->getName(),
                $this->translator->trans(
                    'tl_metamodel_attribute.typeOptions.' . $attribute->get('type'),
                    [],
                    'contao_tl_metamodel_attribute'
                ),
                $attribute->get('type')
            );
        }

        return [$group => $values];
    }

    /**
     * Collect the options for the member group.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function collectMemberOptions(): array
    {
        $this->controller->loadLanguageFile('tl_member');
        $this->controller->loadDataContainer('tl_member');

        $group = $this->translator
            ->trans('tl_metamodel_dcasetting.argument.group.member', [], 'contao_tl_metamodel_dcasetting');

        $values = [];
        foreach ($GLOBALS['TL_DCA']['tl_member']['fields'] as $key => $item) {
            $values['tl_member.' . $key] = \sprintf(
                '%s [%s%s]',
                $item['label'][0],
                $key,
                isset($item['inputType']) ? ' - ' . $item['inputType'] : ''
            );
        }

        return [$group => $values];
    }

    /**
     * Collect the options for the page group.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function collectPageOptions(): array
    {
        $this->controller->loadLanguageFile('tl_page');
        $this->controller->loadDataContainer('tl_page');

        $group = $this->translator
            ->trans('tl_metamodel_dcasetting.argument.group.page', [], 'contao_tl_metamodel_dcasetting');

        $values = [];
        foreach ($GLOBALS['TL_DCA']['tl_page']['fields'] as $key => $item) {
            $values['tl_page.' . $key] = \sprintf(
                '%s [%s%s]',
                $item['label'][0],
                $key,
                isset($item['inputType']) ? ' - ' . $item['inputType'] : ''
            );
        }

        return [$group => $values];
    }

    /**
     * Test if the event is for the correct table and in backend scope.
     *
     * @param GetOptionsEvent $event The event to test.
     *
     * @return bool
     */
    private function wantToHandleGetOptions(GetOptionsEvent $event): bool
    {
        if (!$this->scopeDeterminator->currentScopeIsBackend()) {
            return false;
        }

        $environment = $event->getEnvironment();
        if ('tl_metamodel_dcasetting' !== $environment->getDataDefinition()->getName()) {
            return false;
        }

        if (($event instanceof GetOptionsEvent)
            && $event->getEnvironment()->getDataDefinition()->getName() !== $event->getModel()->getProviderName()
        ) {
            return false;
        }

        return true;
    }
}
