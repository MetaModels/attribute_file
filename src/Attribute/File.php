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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Christopher Boelter <christopher@boelter.eu>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     David Maack <david.maack@arcor.de>
 * @author     MrTool <github@r2pi.de>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Marc Reimann <reimann@mediendepot-ruhr.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Attribute;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\Keywords\KeywordList;
use MetaModels\Attribute\BaseComplex;
use MetaModels\AttributeFileBundle\Doctrine\DBAL\Platforms\Keywords\NotSupportedKeywordList;
use MetaModels\Helper\TableManipulator;
use MetaModels\Helper\ToolboxFile;
use MetaModels\IMetaModel;
use MetaModels\Render\Template;

/**
 * This is the MetaModel attribute class for handling file fields.
 */
class File extends BaseComplex
{
    /**
     * The database connection.
     *
     * @var Connection|null
     */
    private $connection;

    /**
     * Table manipulator instance.
     *
     * @var TableManipulator|null
     */
    private $tableManipulator;

    /**
     * The toolbox for file.
     *
     * @var ToolboxFile|null
     */
    private $toolboxFile;

    /**
     * The string util.
     *
     * @var Adapter|StringUtil|null
     */
    private $stringUtil;

    /**
     * The validator.
     *
     * @var Adapter|Validator|null
     */
    private $validator;

    /**
     * The repository for files.
     *
     * @var Adapter|FilesModel|null
     */
    private $fileRepository;

    /**
     * The contao configurations.
     *
     * @var Adapter|Config|null
     */
    private $config;

    /**
     * The platform reserved keyword list.
     *
     * @var KeywordList
     */
    private $platformReservedWord;

    /**
     * Create a new instance.
     *
     * @param IMetaModel              $metaModel        The MetaModel instance this attribute belongs to.
     * @param array                   $information      The attribute information.
     * @param Connection|null         $connection       The database connection.
     * @param TableManipulator|null   $tableManipulator Table manipulator instance.
     * @param ToolboxFile|null        $toolboxFile      The toolbox for file.
     * @param Adapter|StringUtil|null $stringUtil       The string util.
     * @param Adapter|Validator|null  $validator        The validator.
     * @param Adapter|FilesModel|null $fileRepository   The repository for files.
     * @param Adapter|Config|null     $config           The contao configurations.
     */
    public function __construct(
        IMetaModel $metaModel,
        $information = [],
        Connection $connection = null,
        TableManipulator $tableManipulator = null,
        ToolboxFile $toolboxFile = null,
        Adapter $stringUtil = null,
        Adapter $validator = null,
        Adapter $fileRepository = null,
        Adapter $config = null
    ) {
        parent::__construct($metaModel, $information);

        if (null === $toolboxFile) {
            // @codingStandardsIgnoreStart
            @\trigger_error(
                '"toolboxFile"" is missing. It has to be passed in the constructor.' .
                'Fallback will get removed in MetaModels 3.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $toolboxFile = System::getContainer()->get('metamodels.attribute_file.toolbox.file');
        }

        if (null === $stringUtil) {
            // @codingStandardsIgnoreStart
            @\trigger_error(
                '"stringUtil"" is missing. It has to be passed in the constructor.' .
                'Fallback will get removed in MetaModels 3.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $stringUtil = System::getContainer()->get('contao.framework')->getAdapter(StringUtil::class);
        }

        if (null === $validator) {
            // @codingStandardsIgnoreStart
            @\trigger_error(
                '"validator"" is missing. It has to be passed in the constructor.' .
                'Fallback will get removed in MetaModels 3.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $validator = System::getContainer()->get('contao.framework')->getAdapter(Validator::class);
        }

        if (null === $fileRepository) {
            // @codingStandardsIgnoreStart
            @\trigger_error(
                '"fileRepository"" is missing. It has to be passed in the constructor.' .
                'Fallback will get removed in MetaModels 3.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $fileRepository = System::getContainer()->get('contao.framework')->getAdapter(FilesModel::class);
        }

        if (null === $config) {
            // @codingStandardsIgnoreStart
            @\trigger_error(
                '"config"" is missing. It has to be passed in the constructor.' .
                'Fallback will get removed in MetaModels 3.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $config = System::getContainer()->get('contao.framework')->getAdapter(Config::class);
        }

        $this->connection       = $connection;
        $this->tableManipulator = $tableManipulator;
        $this->toolboxFile      = $toolboxFile;
        $this->stringUtil       = $stringUtil;
        $this->validator        = $validator;
        $this->fileRepository   = $fileRepository;
        $this->config           = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function destroyAUX()
    {
        parent::destroyAUX();
        $metaModel = $this->getMetaModel()->getTableName();

        // Try to delete the column. If it does not exist as we can assume it has been deleted already then.
        $tableColumns = $this->connection->getSchemaManager()->listTableColumns($metaModel);
        if (($colName = $this->getColName()) && \array_key_exists($colName, $tableColumns)) {
            $this->tableManipulator->dropColumn($metaModel, $colName);
        }

        if (\array_key_exists($colName . '__sort', $tableColumns)) {
            $this->tableManipulator->dropColumn($metaModel, $colName . '__sort');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initializeAUX()
    {
        parent::initializeAUX();
        if ($colName = $this->getColName()) {
            $tableName = $this->quoteReservedWord($this->getMetaModel()->getTableName());
            $this->tableManipulator->createColumn($tableName, $this->quoteReservedWord($colName), 'blob NULL');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function searchFor($strPattern)
    {
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select($this->quoteReservedWord('uuid'))
            ->from($this->quoteReservedWord('tl_files'))
            ->where($subSelect->expr()->like($this->quoteReservedWord('path'), ':value'));
        $builder = $this->connection->createQueryBuilder();
        $builder
            ->select($this->quoteReservedWord('id'))
            ->from($this->getMetaModel()->getTableName())
            ->where($builder->expr()->in($this->quoteReservedWord($this->getColName()), $subSelect->getSQL()))
            ->setParameter('value', \str_replace(['*', '?'], ['%', '_'], $strPattern));

        return $builder->execute()->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * {@inheritdoc}
     */
    public function unsetDataFor($arrIds)
    {
        $builder = $this->connection->createQueryBuilder();
        $builder
            ->update($this->quoteReservedWord($this->getMetaModel()->getTableName()))
            ->set($this->quoteReservedWord($this->getColName()), ':null')
            ->where($builder->expr()->in($this->quoteReservedWord('id'), ':values'))
            ->setParameter('values', $arrIds, Connection::PARAM_STR_ARRAY)
            ->setParameter('null', null);

        if ($this->getMetaModel()->hasAttribute($this->getColName() . '__sort')) {
            $builder->set($this->quoteReservedWord($this->getColName() . '__sort'), ':null');
        }

        $builder->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFor($arrIds)
    {
        $builder = $this->connection->createQueryBuilder();

        $idField   = $this->quoteReservedWord('id');
        $aliasFile = $this->quoteReservedWord('file');
        $builder
            ->select($idField, ' ' . $this->quoteReservedWord($this->getColName()) . ' ' . $aliasFile)
            ->from($this->getMetaModel()->getTableName())
            ->where($builder->expr()->in($idField, ':values'))
            ->setParameter('values', $arrIds, Connection::PARAM_STR_ARRAY);

        if ($hasSort = $this->getMetaModel()->hasAttribute($this->getColName() . '__sort')) {
            $sortField     = $this->quoteReservedWord($this->getColName() . '__sort');
            $aliasFileSort = $this->quoteReservedWord('file_sort');
            $builder->addSelect($sortField . ' ' . $aliasFileSort);
        }

        $query = $builder->execute();
        $data  = [];
        while ($result = $query->fetch(\PDO::FETCH_OBJ)) {
            $row = $this->toolboxFile->convertValuesToMetaModels($this->stringUtil->deserialize($result->file, true));

            if ($hasSort) {
                // The sort key be can remove in later version. The new sort key is bin_sorted.
                $row['sort'] = $sorted = $this->stringUtil->deserialize($result->file_sort, true);

                foreach ($this->toolboxFile->convertValuesToMetaModels($sorted) as $sortedKey => $sortedValue) {
                    $row[$sortedKey . '_sorted'] = $sortedValue;
                }

                if (isset($row['sort'])) {
                    // @codingStandardsIgnoreStart
                    @\trigger_error(
                        'The sort key from the attribute file is deprecated since 2.1 and where removed in 3.0' .
                        'Use the key bin_sorted',
                        E_USER_DEPRECATED
                    );
                    // @codingStandardsIgnoreEnd
                }
            }

            $data[$result->id] = $row;
        }

        return $data;
    }

    /**
     * This method is called to store the data for certain items to the database.
     *
     * @param mixed $arrValues The values to be stored into database. Mapping is item id=>value.
     *
     * @return void
     */
    public function setDataFor($arrValues)
    {
        $tableName = $this->getMetaModel()->getTableName();
        $colName   = $this->getColName();
        foreach ($arrValues as $id => $value) {
            if (null === $value) {
                // The sort key be can remove in later version.
                $value = ['bin' => [], 'value' => [], 'path' => [], 'sort' => null];
            }

            $files = ToolboxFile::convertValuesToDatabase($value);

            // Check single file or multiple file.
            if ($this->get('file_multiple')) {
                $files = \serialize($files);
            } else {
                $files = $files[0];
            }

            $this->connection->update(
                $this->quoteReservedWord($tableName),
                [$this->quoteReservedWord($colName) => $files],
                [$this->quoteReservedWord('id') => $id]
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getFilterOptions($idList, $usedOnly, &$arrCount = null)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSettingNames()
    {
        return \array_merge(
            parent::getAttributeSettingNames(),
            [
                'file_multiple',
                'file_customFiletree',
                'file_uploadFolder',
                'file_validFileTypes',
                'file_filesOnly',
                'file_widgetMode',
                'filterable',
                'searchable',
                'mandatory'
            ]
        );
    }

    /**
     * Take the raw data from the DB column and unserialize it.
     *
     * @param mixed $value The array of data from the database.
     *
     * @return array
     */
    public function unserializeData($value)
    {
        return ToolboxFile::convertValuesToMetaModels($this->stringUtil->deserialize($value, true));
    }

    /**
     * Take the data from the system and serialize it for the database.
     *
     * @param mixed $mixValues The data to serialize.
     *
     * @return string An serialized array with binary data or a binary data.
     */
    public function serializeData($mixValues)
    {
        $data = ToolboxFile::convertValuesToDatabase($mixValues ?: ['bin' => [], 'value' => [], 'path' => []]);

        // Check single file or multiple file.
        if ($this->get('file_multiple')) {
            return \serialize($data);
        }

        return $data[0];
    }

    /**
     * Manipulate the field definition for custom file trees.
     *
     * @param array $arrFieldDef The field definition to manipulate.
     *
     * @return void
     */
    private function handleCustomFileTree(&$arrFieldDef)
    {
        if ($this->get('file_uploadFolder')) {
            // Set root path of file chooser depending on contao version.
            $file = null;

            if ($this->validator->isUuid($this->get('file_uploadFolder'))) {
                $file = $this->fileRepository->findByUuid($this->get('file_uploadFolder'));
            }

            // Check if we have a file.
            if (null !== $file) {
                $arrFieldDef['eval']['path'] = $file->path;
            } else {
                // Fallback.
                $arrFieldDef['eval']['path'] = $this->get('file_uploadFolder');
            }
        }

        if ($this->get('file_validFileTypes')) {
            $arrFieldDef['eval']['extensions'] = $this->get('file_validFileTypes');
        }

        switch ($this->get('file_filesOnly')) {
            case '1':
                // Files only.
                $fieldDefinition['eval']['filesOnly'] = true;
                break;
            case '2':
                // Folders only.
                $fieldDefinition['eval']['files'] = false;
                break;
            default:
                // Files and files possible.
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($arrOverrides = [])
    {
        $fieldDefinition = parent::getFieldDefinition($arrOverrides);

        $fieldDefinition['inputType']          = 'fileTree';
        $fieldDefinition['eval']['files']      = true;
        $fieldDefinition['eval']['extensions'] = $this->config->get('allowedDownload');
        $fieldDefinition['eval']['multiple']   = (bool) $this->get('file_multiple');

        $widgetMode = $this->getOverrideValue('file_widgetMode', $arrOverrides);

        if (('normal' !== $widgetMode)
            && ((bool) $this->get('file_multiple'))
        ) {
            $fieldDefinition['eval']['orderField'] = $this->getColName() . '__sort';
        }

        $fieldDefinition['eval']['isDownloads'] = ('downloads' === $widgetMode);
        $fieldDefinition['eval']['isGallery']   = ('gallery' === $widgetMode);

        if ($this->get('file_multiple')) {
            $fieldDefinition['eval']['fieldType'] = 'checkbox';
        } else {
            $fieldDefinition['eval']['fieldType'] = 'radio';
        }

        if ($this->get('file_customFiletree')) {
            $this->handleCustomFileTree($fieldDefinition);
        }

        return $fieldDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        return $this->get('file_multiple') ? ($varValue['bin'] ?? null) : ($varValue['bin'][0] ?? null);
    }

    /**
     * {@inheritdoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        return ToolboxFile::convertUuidsOrPathsToMetaModels((array) $varValue);
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareTemplate(Template $template, $rowData, $settings)
    {
        parent::prepareTemplate($template, $rowData, $settings);

        $value = $rowData[$this->getColName()];

        // No data and show image, check placeholder.
        if (!$value['bin']
            && null !== $settings->get('file_showImage')
            && null !== ($placeholder = $settings->get('file_placeholder'))) {
            $value['bin'][]   = $placeholder;
            $value['value'][] = StringUtil::binToUuid($placeholder);
        }

        $toolbox = clone $this->toolboxFile;

        $toolbox
            ->setBaseLanguage($this->getMetaModel()->getActiveLanguage())
            ->setFallbackLanguage($this->getMetaModel()->getFallbackLanguage())
            ->setLightboxId(
                \sprintf(
                    '%s.%s.%s',
                    $this->getMetaModel()->getTableName(),
                    $settings->get('id'),
                    $rowData['id']
                )
            )
            ->setShowImages($settings->get('file_showImage'));

        if ($this->get('file_validFileTypes')) {
            $toolbox->setAcceptedExtensions($this->get('file_validFileTypes'));
        }

        if ($settings->get('file_imageSize')) {
            $toolbox->setResizeImages($settings->get('file_imageSize'));
        }

        if (isset($value['value'])) {
            foreach ($value['value'] as $strFile) {
                $toolbox->addPathById($strFile);
            }
        } elseif (\is_array($value)) {
            foreach ($value as $strFile) {
                $toolbox->addPathById($strFile);
            }
        } else {
            $toolbox->addPathById($value);
        }

        $toolbox->resolveFiles();
        $data = $toolbox->sortFiles($settings->get('file_sortBy'), ($value['bin_sorted'] ?? []));

        $template->files = $data['files'];
        $template->src   = $data['source'];
    }

    /**
     * Quote the reserved platform key word.
     *
     * @param string $word The key word.
     *
     * @return string
     */
    private function quoteReservedWord(string $word): string
    {
        if (null === $this->platformReservedWord) {
            try {
                $this->platformReservedWord = $this->connection->getDatabasePlatform()->getReservedKeywordsList();
            } catch (DBALException $exception) {
                // Add the not support key word list, if the platform has not a list of keywords.
                $this->platformReservedWord = new NotSupportedKeywordList();
            }
        }

        if (false === $this->platformReservedWord->isKeyword($word)) {
            return $word;
        }

        return $this->connection->quoteIdentifier($word);
    }
}
