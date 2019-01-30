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
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Attribute;

use Contao\CoreBundle\Image\ImageFactoryInterface;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Contao\Config;
use Contao\FilesModel;
use Contao\Validator;
use MetaModels\Attribute\BaseComplex;
use MetaModels\Attribute\ISchemaManagedAttribute;
use MetaModels\Helper\TableManipulator;
use MetaModels\IMetaModel;
use MetaModels\Render\Template;
use MetaModels\Helper\ToolboxFile;

/**
 * This is the MetaModel attribute class for handling file fields.
 */
class File extends BaseComplex implements ISchemaManagedAttribute
{
    /**
     * The image factory.
     *
     * @var ImageFactoryInterface
     */
    private $imageFactory;

    /**
     * The installation root dir.
     *
     * @var string
     */
    private $rootPath;

    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Table manipulator instance.
     *
     * @var TableManipulator
     */
    private $tableManipulator;

    /**
     * Create a new instance.
     *
     * @param IMetaModel            $objMetaModel     The MetaModel instance this attribute belongs to.
     * @param array                 $arrData          The attribute information array.
     * @param Connection            $connection       The database connection.
     * @param TableManipulator      $tableManipulator Table manipulator instance.
     * @param ImageFactoryInterface $imageFactory     The image factory to use.
     * @param string                $rootPath         The root path.
     */
    public function __construct(
        IMetaModel $objMetaModel,
        $arrData = [],
        Connection $connection = null,
        TableManipulator $tableManipulator = null,
        ImageFactoryInterface $imageFactory = null,
        $rootPath = null
    ) {
        parent::__construct($objMetaModel, $arrData);
        if (null === $imageFactory) {
            // @codingStandardsIgnoreStart
            @\trigger_error(
                'No "ImageFactoryInterface" passed. It has to be passed in the constructor.' .
                'Fallback will get removed in MetaModels 3.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $imageFactory = System::getContainer()->get('contao.image.image_factory');
        }

        if (null === $rootPath) {
            // @codingStandardsIgnoreStart
            @\trigger_error(
                '"rootPath"" is missing. It has to be passed in the constructor.' .
                'Fallback will get removed in MetaModels 3.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $rootPath = System::getContainer()->getParameter('kernel.project_dir');
        }

        $this->imageFactory     = $imageFactory;
        $this->rootPath         = $rootPath;
        $this->connection       = $connection;
        $this->tableManipulator = $tableManipulator;
    }

    /**
     * {@inheritdoc}
     */
    public function destroyAUX()
    {
        parent::destroyAUX();
        $metaModel = $this->getMetaModel()->getTableName();
        // Try to delete the column. If it does not exist as we can assume it has been deleted already then.
        if (($colName = $this->getColName())
            && !empty($this->connection->getSchemaManager()->listTableColumns($metaModel)[$colName])
        ) {
            $this->tableManipulator->dropColumn($metaModel, $colName);
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
            $tableName = $this->getMetaModel()->getTableName();
            $this->tableManipulator->createColumn($tableName, $colName, 'blob NULL');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function searchFor($strPattern)
    {
        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('uuid')
            ->from('tl_files')
            ->where($subSelect->expr()->like('path', ':value'));
        $builder = $this->connection->createQueryBuilder();
        $builder
            ->select('id')
            ->from($this->getMetaModel()->getTableName())
            ->where($builder->expr()->in($this->getColName(), $subSelect->getSQL()))
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
            ->update($this->getMetaModel()->getTableName())
            ->set($this->getColName(), ':null')
            ->where($builder->expr()->in('id', ':values'))
            ->setParameter('values', $arrIds, Connection::PARAM_STR_ARRAY)
            ->setParameter('null', null);

        if ($this->getMetaModel()->hasAttribute($this->getColName() . '__sort')) {
            $builder->set($this->getColName() . '__sort', ':null');
        }

        $builder->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFor($arrIds)
    {
        $builder = $this->connection->createQueryBuilder();

        $builder
            ->select('id', $this->getColName() . ' AS file')
            ->from($this->getMetaModel()->getTableName())
            ->where($builder->expr()->in('id', ':values'))
            ->setParameter('values', $arrIds, Connection::PARAM_STR_ARRAY);

        if ($hasSort = $this->getMetaModel()->hasAttribute($this->getColName() . '__sort')) {
            $builder->addSelect($this->getColName() . '__sort AS file_sort');
        }

        $query = $builder->execute();
        $data  = [];
        while ($result = $query->fetch(\PDO::FETCH_ASSOC)) {
            $row = ToolboxFile::convertValuesToMetaModels(StringUtil::deserialize($result['file'], true));

            if ($hasSort) {
                $row['sort'] = StringUtil::deserialize($result['file_sort'], true);
            }

            $data[$result['id']] = $row;
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
        foreach ($arrValues as $id => $varData) {
            if ($varData === null) {
                $varData = ['bin' => [], 'value' => [], 'path' => [], 'sort' => null];
            }

            $files = ToolboxFile::convertValuesToDatabase($varData);

            // Check single file or multiple file.
            if ($this->get('file_multiple')) {
                $files = \serialize($files);
            } else {
                $files = $files[0];
            }

            $this->connection->update($tableName, [$colName => $files], ['id' => $id]);
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
                'mandatory',
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
        return ToolboxFile::convertValuesToMetaModels(StringUtil::deserialize($value, true));
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
        if ($mixValues === null) {
            $mixValues = ['bin' => [], 'value' => [], 'path' => []];
        }
        $arrData = ToolboxFile::convertValuesToDatabase($mixValues);

        // Check single file or multiple file.
        if ($this->get('file_multiple')) {
            $mixValues = \serialize($arrData);
        } else {
            $mixValues = $arrData[0];
        }

        return $mixValues;
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
        if (\strlen($this->get('file_uploadFolder'))) {
            // Set root path of file chooser depending on contao version.
            $objFile = null;

            if (Validator::isUuid($this->get('file_uploadFolder'))) {
                $objFile = FilesModel::findByUuid($this->get('file_uploadFolder'));
            }

            // Check if we have a file.
            if ($objFile != null) {
                $arrFieldDef['eval']['path'] = $objFile->path;
            } else {
                // Fallback.
                $arrFieldDef['eval']['path'] = $this->get('file_uploadFolder');
            }
        }

        if (\strlen($this->get('file_validFileTypes'))) {
            $arrFieldDef['eval']['extensions'] = $this->get('file_validFileTypes');
        }

        if (\strlen($this->get('file_filesOnly'))) {
            $arrFieldDef['eval']['filesOnly'] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($arrOverrides = [])
    {
        $arrFieldDef = parent::getFieldDefinition($arrOverrides);

        $arrFieldDef['inputType']          = 'fileTree';
        $arrFieldDef['eval']['files']      = true;
        $arrFieldDef['eval']['extensions'] = Config::get('allowedDownload');
        $arrFieldDef['eval']['multiple']   = (bool) $this->get('file_multiple');

        $widgetMode = $this->getOverrideValue('file_widgetMode', $arrOverrides);

        if (('normal' !== $widgetMode)
            && ((bool) $this->get('file_multiple'))
        ) {
            $arrFieldDef['eval']['orderField'] = $this->getColName() . '__sort';
        }

        $arrFieldDef['eval']['isDownloads'] = ('downloads' === $widgetMode);
        $arrFieldDef['eval']['isGallery']   = ('gallery' === $widgetMode);

        if ($this->get('file_multiple')) {
            $arrFieldDef['eval']['fieldType'] = 'checkbox';
        } else {
            $arrFieldDef['eval']['fieldType'] = 'radio';
        }

        if ($this->get('file_customFiletree')) {
            $this->handleCustomFileTree($arrFieldDef);
        }

        return $arrFieldDef;
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        if (empty($varValue)) {
            return null;
        }

        if (!$this->get('file_multiple')) {
            return isset($varValue['bin'][0]) ? $varValue['bin'][0] : null;
        }

        return $varValue['bin'];
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
    protected function prepareTemplate(Template $objTemplate, $arrRowData, $objSettings)
    {
        parent::prepareTemplate($objTemplate, $arrRowData, $objSettings);

        $objToolbox = new ToolboxFile($this->imageFactory, $this->rootPath);

        // No data, nothing to do.
        if (!$arrRowData[$this->getColName()]) {
            return;
        }

        $objToolbox->setBaseLanguage($this->getMetaModel()->getActiveLanguage());

        $objToolbox->setFallbackLanguage($this->getMetaModel()->getFallbackLanguage());

        $objToolbox->setLightboxId(\sprintf(
            '%s.%s.%s',
            $this->getMetaModel()->getTableName(),
            $objSettings->get('id'),
            $arrRowData['id']
        ));

        if (\strlen($this->get('file_validFileTypes'))) {
            $objToolbox->setAcceptedExtensions($this->get('file_validFileTypes'));
        }

        $objToolbox->setShowImages($objSettings->get('file_showImage'));

        if ($objSettings->get('file_imageSize')) {
            $objToolbox->setResizeImages($objSettings->get('file_imageSize'));
        }

        $value = $arrRowData[$this->getColName()];

        if (isset($value['value'])) {
            foreach ($value['value'] as $strFile) {
                $objToolbox->addPathById($strFile);
            }
        } elseif (\is_array($value)) {
            foreach ($value as $strFile) {
                $objToolbox->addPathById($strFile);
            }
        } else {
            $objToolbox->addPathById($value);
        }

        $objToolbox->resolveFiles();
        $arrData = $objToolbox->sortFiles($objSettings->get('file_sortBy'), $value['sort']);

        $objTemplate->files = $arrData['files'];
        $objTemplate->src   = $arrData['source'];
    }
}
