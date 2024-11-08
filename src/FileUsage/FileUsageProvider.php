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

namespace MetaModels\AttributeFileBundle\FileUsage;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\FilesModel;
use Contao\Model\Collection;
use Contao\StringUtil;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use InspiredMinds\ContaoFileUsage\Provider\FileUsageProviderInterface;
use InspiredMinds\ContaoFileUsage\Result\ResultInterface;
use InspiredMinds\ContaoFileUsage\Result\ResultsCollection;
use MetaModels\AttributeFileBundle\Attribute\File;
use MetaModels\CoreBundle\FileUsage\MetaModelsMultipleResult;
use MetaModels\CoreBundle\FileUsage\MetaModelsSingleResult;
use MetaModels\IFactory;
use MetaModels\IMetaModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This class supports the Contao extension 'file usage'.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FileUsageProvider implements FileUsageProviderInterface
{
    private string $refererId = '';

    public function __construct(
        private readonly IFactory $factory,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RequestStack $requestStack,
        private readonly ContaoCsrfTokenManager $csrfTokenManager,
        private readonly string $csrfTokenName,
    ) {
    }

    public function find(): ResultsCollection
    {
        $this->refererId = $this->requestStack->getCurrentRequest()?->attributes->get('_contao_referer_id') ?? '';

        $allTables = $this->factory->collectNames();

        $collection = new ResultsCollection();
        foreach ($allTables as $table) {
            $collection->mergeCollection($this->processTable($table));
        }

        return $collection;
    }

    private function processTable(string $table): ResultsCollection
    {
        $collection = new ResultsCollection();
        $metaModel  = $this->factory->getMetaModel($table);
        assert($metaModel instanceof IMetaModel);

        $allIds = $metaModel->getIdsFromFilter($metaModel->getEmptyFilter());
        foreach ($metaModel->getAttributes() as $attribute) {
            if (!$attribute instanceof File) {
                continue;
            }

            $attributeName = $attribute->getColName();

            $allData = $attribute->getDataFor($allIds);
            if ($attribute->get('file_multiple')) {
                foreach ($allData as $itemId => $selectedFiles) {
                    $collection->mergeCollection(
                        $this->addMultipleFileReferences($selectedFiles['value'], $table, $attributeName, $itemId)
                    );
                }
                continue;
            }

            foreach ($allData as $itemId => $selectedFiles) {
                if([] === $selectedFiles['value']) {
                    continue;
                }
                $collection->addResult(
                    $selectedFiles['value'][0],
                    $this->createFileResult($table, $attributeName, $itemId, false)
                );
            }
        }

        return $collection;
    }

    private function addMultipleFileReferences(
        array $fileUuids,
        string $tableName,
        string $attributeName,
        string $itemId,
    ): ResultsCollection {
        $collection = new ResultsCollection();
        foreach ($fileUuids as $uuid) {
            $collection->addResult($uuid, $this->createFileResult($tableName, $attributeName, $itemId, true));
            // Also add children, if the reference is a folder.
            $file = FilesModel::findByUuid($uuid);
            if (null !== $file && 'folder' === $file->type) {
                $files = FilesModel::findByPid($uuid);
                if (null === $files) {
                    continue;
                }
                assert($files instanceof Collection);
                foreach ($files as $child) {
                    $collection->addResult(
                        StringUtil::binToUuid($child->uuid),
                        $this->createFileResult($tableName, $attributeName, $itemId, true)
                    );
                }
            }
        }

        return $collection;
    }

    private function createFileResult(
        string $tableName,
        string $attributeName,
        string $itemId,
        bool $isMultiple
    ): ResultInterface {
        if ($isMultiple) {
            return new MetaModelsMultipleResult(
                $tableName,
                $attributeName,
                $itemId,
                $this->urlGenerator->generate(
                    'metamodels.metamodel',
                    [
                        'tableName' => $tableName,
                        'act'       => 'edit',
                        'id'        => ModelId::fromValues($tableName, $itemId)->getSerialized(),
                        'ref'       => $this->refererId,
                        'rt'        => $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue(),
                    ]
                )
            );
        }

        return new MetaModelsSingleResult(
            $tableName,
            $attributeName,
            $itemId,
            $this->urlGenerator->generate(
                'metamodels.metamodel',
                [
                    'tableName' => $tableName,
                    'act'       => 'edit',
                    'id'        => ModelId::fromValues($tableName, $itemId)->getSerialized(),
                    'ref'       => $this->refererId,
                    'rt'        => $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue(),
                ]
            )
        );
    }
}
