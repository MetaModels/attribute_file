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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Attribute;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\FilesModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Helper\TableManipulator;
use MetaModels\Helper\ToolboxFile;
use Validator;

/**
 * Attribute type factory for file attributes.
 */
class AttributeTypeFactory implements IAttributeTypeFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Table manipulator.
     *
     * @var TableManipulator
     */
    protected $tableManipulator;

    /**
     * The toolbox for file.
     *
     * @var ToolboxFile
     */
    private ToolboxFile $toolboxFile;

    /**
     * The string util.
     *
     * @var Adapter
     */
    private Adapter $stringUtil;

    /**
     * The validator.
     *
     * @var Adapter
     */
    private Adapter $validator;

    /**
     * The repository for files.
     *
     * @var Adapter
     */
    private Adapter $fileRepository;

    /**
     * The contao configurations.
     *
     * @var Adapter
     */
    private Adapter $config;

    /**
     * {@inheritDoc}
     *
     * @param Connection       $connection       The database connection.
     * @param TableManipulator $tableManipulator The table manipulator.
     * @param ToolboxFile      $toolboxFile      The toolbox for file.
     * @param Adapter          $stringUtil       The string util.
     * @param Adapter          $validator        The validator.
     * @param Adapter          $fileRepository   The repository for files.
     * @param Adapter          $config           The contao configurations.
     */
    public function __construct(
        Connection $connection,
        TableManipulator $tableManipulator,
        ToolboxFile $toolboxFile,
        Adapter $stringUtil,
        Adapter $validator,
        Adapter $fileRepository,
        Adapter $config
    ) {
        $this->connection       = $connection;
        $this->tableManipulator = $tableManipulator;
        $this->toolboxFile      = $toolboxFile;
        $this->stringUtil       = $stringUtil;
        $this->validator        = $validator;
        $this->fileRepository   = $fileRepository;
        $this->config           = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeName()
    {
        return 'file';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeIcon()
    {
        return 'bundles/metamodelsattributefile/file.png';
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $metaModel)
    {
        return new File(
            $metaModel,
            $information,
            $this->connection,
            $this->tableManipulator,
            $this->toolboxFile,
            $this->stringUtil,
            $this->validator,
            $this->fileRepository,
            $this->config
        );
    }

    /**
     * {@inheritDoc}
     */
    public function isTranslatedType()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSimpleType()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isComplexType()
    {
        return true;
    }
}
