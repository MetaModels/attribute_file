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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_file/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeFileBundle\Attribute;

use Contao\CoreBundle\Image\ImageFactoryInterface;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Helper\TableManipulator;

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
     * {@inheritDoc}
     *
     * @param Connection       $connection        The database connection.
     * @param TableManipulator $tableManipulator  The table manipulator.
     * @param ImageFactoryInterface $imageFactory The image factory to use.
     * @param string                $rootPath     The root path.
     */
    public function __construct(
        Connection $connection,
        TableManipulator $tableManipulator,
        ImageFactoryInterface $imageFactory,
        $rootPath
    ) {
        $this->connection       = $connection;
        $this->tableManipulator = $tableManipulator;
        $this->imageFactory     = $imageFactory;
        $this->rootPath         = $rootPath;
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
        $sortAttribute = $information['colname'] . '__sort';
        $file          = new File(
            $metaModel,
            $information,
            $this->connection,
            $this->tableManipulator,
            $this->imageFactory,
            $this->rootPath
        );

        if (empty($information['file_multiple'])
            || $metaModel->hasAttribute($sortAttribute)
        ) {
            return $file;
        }

        try {
            $this->tableManipulator->checkColumnExists($metaModel->getTableName(), $sortAttribute);
        } catch (\Exception $e) {
            return $file;
        }

        // Inject ad-hoc order attribute.
        $order = new FileOrder($metaModel, $sortAttribute, $this->connection);
        $metaModel->addAttribute($order);

        return $file;
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
