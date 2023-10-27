<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_Translation
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\Translation\Model;

use Exception;
use MageINIC\Translation\Api\Data\TranslationInterface;
use MageINIC\Translation\Api\TranslationRepositoryInterface;
use MageINIC\Translation\Model\ResourceModel\Translation as ResourceTranslation;
use MageINIC\Translation\Model\ResourceModel\Translation\CollectionFactory as TranslationCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * MageINIC_Translation Class TranslationRepository
 */
class TranslationRepository implements TranslationRepositoryInterface
{
    /**
     * @var TranslationFactory
     */
    public TranslationFactory $translationFactory;

    /**
     * @var ResourceTranslation
     */
    protected ResourceTranslation $resource;

    /**
     * @var TranslationCollectionFactory
     */
    protected TranslationCollectionFactory $translationCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * TranslationRepository Constructor
     *
     * @param ResourceTranslation $resource
     * @param TranslationFactory $translationFactory
     * @param TranslationCollectionFactory $translationCollectionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ResourceTranslation          $resource,
        TranslationFactory           $translationFactory,
        TranslationCollectionFactory $translationCollectionFactory,
        DataObjectHelper             $dataObjectHelper,
        DataObjectProcessor          $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->translationFactory = $translationFactory;
        $this->translationCollectionFactory = $translationCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(TranslationInterface $translation): TranslationInterface
    {
        try {
            $this->resource->save($translation);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $translation;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $Id): bool
    {
        return $this->delete($this->getById($Id));
    }

    /**
     * @inheritdoc
     */
    public function delete(TranslationInterface $translation): bool
    {
        try {
            $this->resource->delete($translation);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $Id): TranslationInterface
    {
        $translation = $this->translationFactory->create();
        $this->resource->load($translation, $Id);
        if (!$translation->getId()) {
            throw new NoSuchEntityException(__('The Translation with the "%1" ID doesn\'t exist.', $Id));
        }
        return $translation;
    }
}
