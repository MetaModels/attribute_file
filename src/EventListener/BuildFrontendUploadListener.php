<?php

/**
 * This file is part of MetaModels/attribute_file.
 *
 * (c) 2012-2022 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_file
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

declare(strict_types=1);

namespace MetaModels\AttributeFileBundle\EventListener;

use Contao\FrontendUser;
use Contao\InsertTags;
use Contao\StringUtil;
use ContaoCommunityAlliance\DcGeneral\Contao\InputProvider;
use ContaoCommunityAlliance\DcGeneral\Contao\RequestScopeDeterminatorAwareTrait;
use ContaoCommunityAlliance\DcGeneral\InputProviderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\CoreBundle\Contao\InsertTag\ReplaceParam;
use MetaModels\CoreBundle\Contao\InsertTag\ReplaceTableName;
use MetaModels\DcGeneral\Events\MetaModel\BuildAttributeEvent;
use MetaModels\ViewCombination\ViewCombination;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This event build the attribute for upload field, in the frontend editing scope.
 */
final class BuildFrontendUploadListener
{
    use RequestScopeDeterminatorAwareTrait;

    /**
     * The view combinations.
     *
     * @var ViewCombination
     */
    private $viewCombination;

    /**
     * The token storage.
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * The property information from the input screen.
     *
     * @var array
     */
    private $information;

    /**
     * The insert tag replacer, for replace the table name.
     *
     * @var ReplaceTableName
     */
    private $replaceTableName;

    /**
     * The insert tag replacer, for replace parameters.
     *
     * @var ReplaceParam
     */
    private $replaceParam;

    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The input provider.
     *
     * @var InputProviderInterface
     */
    private $inputProvider;

    /**
     * The autoincrement.
     *
     * @var int
     */
    private $autoincrement;

    /**
     * The id column.
     *
     * @var Column
     */
    private $columnId;

    /**
     * The constructor.
     *
     * @param ViewCombination       $viewCombination  The view combination.
     * @param TokenStorageInterface $tokenStorage     The token storage.
     * @param ReplaceTableName      $replaceTableName The insert tag replacer, for replace the table name.
     * @param ReplaceParam          $replaceParam     The insert tag replacer, for replace parameters.
     * @param Connection            $connection       The database connection.
     */
    public function __construct(
        ViewCombination $viewCombination,
        TokenStorageInterface $tokenStorage,
        ReplaceTableName $replaceTableName,
        ReplaceParam $replaceParam,
        Connection $connection
    ) {
        $this->viewCombination  = $viewCombination;
        $this->tokenStorage     = $tokenStorage;
        $this->replaceTableName = $replaceTableName;
        $this->replaceParam     = $replaceParam;
        $this->connection       = $connection;
    }

    /**
     * Build the attribute for single upload field, in the frontend editing scope.
     *
     * @param BuildAttributeEvent $event The event.
     *
     * @return void
     */
    public function __invoke(BuildAttributeEvent $event): void
    {
        if (!$this->wantToHandle($event)) {
            return;
        }

        $this->addWidgetModeInformationToProperty($event);
    }

    /**
     * Add widget mode information to the property extra information.
     *
     * @param BuildAttributeEvent $event The event.
     *
     * @return void
     */
    private function addWidgetModeInformationToProperty(BuildAttributeEvent $event): void
    {
        $property =
            $event->getContainer()->getPropertiesDefinition()->getProperty($event->getAttribute()->getColName());

        $property->setWidgetType('uploadOnSteroids');

        $extra = [
            'doNotOverwrite'        => $this->information['fe_widget_file_doNotOverwrite'],
            'deselect'              => (bool) $this->information['fe_widget_file_deselect'],
            'delete'                => (bool) $this->information['fe_widget_file_delete'],
            'uploadFolder'          => $this->getUserHomeDir() ?: $this->getTargetFolder(),
            'extendFolder'          => $this->getExtendFolder($event),
            'normalizeExtendFolder' => (bool) $this->information['fe_widget_file_normalize_extend_folder'],
            'normalizeFilename'     => (bool) $this->information['fe_widget_file_normalize_filename'],
            'prefixFilename'        => $this->getPrefixFilename($event),
            'postfixFilename'       => $this->getPostfixFilename($event),
            'storeFile'             => true,
            'imageSize'             => $this->information['fe_widget_file_imageSize'],
            'sortBy'                => $this->information['fe_widget_file_sortBy'],
        ];

        $previewModes = ['fe_single_upload_preview', 'fe_multiple_upload_preview'];
        if (\in_array($this->information['file_widgetMode'], $previewModes, true)) {
            $extra['showThumbnail'] = true;
        }

        $multipleModes     = ['fe_multiple_upload', 'fe_multiple_upload_preview'];
        $extra['multiple'] = false;
        if (\in_array($this->information['file_widgetMode'], $multipleModes, true)) {
            $extra['multiple'] = true;
        }

        if ($this->storeFileToTempFolder($extra)) {
            $extra['useTempFolder']     = true;
            $extra['deleteTempFolder']  = true;
            $extra['moveToDestination'] = true;
        }

        $property->setExtra(\array_merge($property->getExtra(), $extra));

        // todo support sorting file. Can be remove if this attribute support order not has hack.
        $properties    = $event->getContainer()->getPropertiesDefinition();
        $propertyExtra = $property->getExtra();
        if (isset($propertyExtra['orderField']) && $properties->hasProperty($propertyExtra['orderField'])) {
            $orderProperty = $properties->getProperty($propertyExtra['orderField']);
            $properties->removeProperty($orderProperty);
        }
    }

    /**
     * Use the user home directory as base folder, if is configured and the user is authenticated.
     *
     * @return string|null
     */
    private function getUserHomeDir(): ?string
    {
        /** @var FrontendUser $feUser */
        if ($this->information['fe_widget_file_useHomeDir']
            && ($user = $this->tokenStorage->getToken())
            && ($feUser = $user->getUser())
            && $feUser->assignDir
            && $feUser->homeDir
        ) {
            return $feUser->homeDir;
        }

        return null;
    }

    /**
     * Use the target folder as base folder, if is configured and the user is not authenticated.
     *
     * @return string|null
     */
    private function getTargetFolder(): ?string
    {
        return $this->information['fe_widget_file_uploadFolder'] ?: null;
    }

    /**
     * Get the extend folder.
     *
     * @param BuildAttributeEvent $event The event.
     *
     * @return string|null
     */
    private function getExtendFolder(BuildAttributeEvent $event): ?string
    {
        if (!($extendFolder = $this->information['fe_widget_file_extend_folder'])
            || (false === \strpos($extendFolder, '{{'))
        ) {
            return $extendFolder ?: null;
        }

        return $this->replaceInsertTag($event, $extendFolder);
    }

    /**
     * Get the prefix for the filename.
     *
     * @param BuildAttributeEvent $event The event.
     *
     * @return string|null
     */
    private function getPrefixFilename(BuildAttributeEvent $event): ?string
    {
        if (!($extendFolder = $this->information['fe_widget_file_prefix_filename'])
            || (false === \strpos($extendFolder, '{{'))
        ) {
            return $extendFolder ?: null;
        }

        return $this->replaceInsertTag($event, $extendFolder);
    }

    /**
     * Get the postfix for the filename.
     *
     * @param BuildAttributeEvent $event The event.
     *
     * @return string|null
     */
    private function getPostfixFilename(BuildAttributeEvent $event): ?string
    {
        if (!($extendFolder = $this->information['fe_widget_file_postfix_filename'])
            || (false === \strpos($extendFolder, '{{'))
        ) {
            return $extendFolder ?: null;
        }

        return $this->replaceInsertTag($event, $extendFolder);
    }

    /**
     * Replace the insert tag.
     *
     * @param BuildAttributeEvent $event   The event.
     * @param string              $replace The replacement.
     *
     * @return string
     */
    private function replaceInsertTag(BuildAttributeEvent $event, string $replace): string
    {
        if ((!$this->autoincrement || !$this->columnId)
            || !$this->getInputProvider()->hasParameter('act')
            || ('create' === $this->getInputProvider()->getParameter('act'))
        ) {
            $tableDetails = $this->connection->getSchemaManager()->listTableDetails($event->getContainer()->getName());
            foreach ($tableDetails->getColumns() as $column) {
                if (!$column->getAutoincrement()) {
                    continue;
                }

                $this->columnId = $column;
                break;
            }

            if ($this->columnId) {
                $this->autoincrement = (int) $tableDetails->getOption('autoincrement');
            }
        }

        if ($this->autoincrement && $this->columnId) {
            $this->getInputProvider()->setValue($this->columnId->getName(), $this->autoincrement);
        }

        $replaced = (string) $this->replaceParam->replace(
            $this->replaceTableName->replace(
                $event->getContainer()->getName(),
                StringUtil::decodeEntities($replace)
            )
        );

        if ($this->autoincrement && $this->columnId) {
            $this->getInputProvider()->unsetValue($this->columnId->getName());
        }

        if ((false === \strpos($replaced, '{{'))) {
            return $replaced;
        }

        $replacer = new InsertTags();
        return $replacer->replace($replaced);
    }

    /**
     * Detect for store file in a temporary folder.
     *
     * @param array $extra The extra information.
     *
     * @return bool
     */
    private function storeFileToTempFolder(array $extra): bool
    {
        return (isset($extra['extendFolder']) && $extra['extendFolder'])
               // Test if in the extend folder path find insert tag.
               && (false !== \strpos($extra['extendFolder'], '{{'));
    }

    /**
     * Get the input provider.
     *
     * @return InputProviderInterface
     */
    private function getInputProvider(): InputProviderInterface
    {
        if (null === $this->inputProvider) {
            $this->inputProvider = new InputProvider();
        }

        return $this->inputProvider;
    }

    /**
     * Detect if in the right scope and the attribute is configured as single upload field.
     *
     * @param BuildAttributeEvent $event The event.
     *
     * @return bool
     */
    private function wantToHandle(BuildAttributeEvent $event): bool
    {
        return $this->scopeDeterminator->currentScopeIsFrontend()
               && !$this->scopeDeterminator->currentScopeIsUnknown()
               && $this->isSingleUploadField($event);
    }

    /**
     * Detect if is configured as single upload field.
     *
     * @param BuildAttributeEvent $event The event.
     *
     * @return bool
     */
    private function isSingleUploadField(BuildAttributeEvent $event): bool
    {
        $properties = [];
        if (!(($attribute = $event->getAttribute()) instanceof File)
            || !($inputScreen = $this->viewCombination->getScreen($event->getContainer()->getName()))
            || !(isset($inputScreen['properties']) && ($properties = $inputScreen['properties']))
        ) {
            return false;
        }

        $supportedModes = [
            'fe_single_upload',
            'fe_single_upload_preview',
            'fe_multiple_upload',
            'fe_multiple_upload_preview'
        ];
        $information    = $properties[\array_flip(\array_column($properties, 'attr_id'))[$attribute->get('id')]];
        if (!isset($information['file_widgetMode'])
            || !\in_array($information['file_widgetMode'], $supportedModes, true)
        ) {
            return false;
        }

        $this->information = $information;

        return true;
    }
}
