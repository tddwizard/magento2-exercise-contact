<?php


namespace TddWizard\ExerciseContact\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;
use TddWizard\ExerciseContact\Api\Data\InquiryInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use TddWizard\ExerciseContact\Api\Data\InquirySearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;
use TddWizard\ExerciseContact\Model\ResourceModel\Inquiry as ResourceInquiry;
use TddWizard\ExerciseContact\Model\ResourceModel\Inquiry\CollectionFactory as InquiryCollectionFactory;

class InquiryRepository implements InquiryRepositoryInterface
{

    protected $searchResultsFactory;

    protected $dataObjectProcessor;

    protected $dataInquiryFactory;

    protected $dataObjectHelper;

    private $storeManager;

    protected $resource;

    protected $InquiryFactory;

    protected $InquiryCollectionFactory;


    /**
     * @param ResourceInquiry $resource
     * @param InquiryFactory $inquiryFactory
     * @param InquiryInterfaceFactory $dataInquiryFactory
     * @param InquiryCollectionFactory $inquiryCollectionFactory
     * @param InquirySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceInquiry $resource,
        InquiryFactory $inquiryFactory,
        InquiryInterfaceFactory $dataInquiryFactory,
        InquiryCollectionFactory $inquiryCollectionFactory,
        InquirySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->inquiryFactory = $inquiryFactory;
        $this->inquiryCollectionFactory = $inquiryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataInquiryFactory = $dataInquiryFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \TddWizard\ExerciseContact\Api\Data\InquiryInterface $inquiry
    ) {
        /* if (empty($inquiry->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $inquiry->setStoreId($storeId);
        } */
        try {
            $this->resource->save($inquiry);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the inquiry: %1',
                $exception->getMessage()
            ));
        }
        return $inquiry;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($inquiryId)
    {
        $inquiry = $this->inquiryFactory->create();
        $inquiry->load($inquiryId);
        if (!$inquiry->getId()) {
            throw new NoSuchEntityException(__('Inquiry with id "%1" does not exist.', $inquiryId));
        }
        return $inquiry;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $collection = $this->inquiryCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];
        
        foreach ($collection as $inquiryModel) {
            $inquiryData = $this->dataInquiryFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $inquiryData,
                $inquiryModel->getData(),
                'TddWizard\ExerciseContact\Api\Data\InquiryInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $inquiryData,
                'TddWizard\ExerciseContact\Api\Data\InquiryInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \TddWizard\ExerciseContact\Api\Data\InquiryInterface $inquiry
    ) {
        try {
            $this->resource->delete($inquiry);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Inquiry: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($inquiryId)
    {
        return $this->delete($this->getById($inquiryId));
    }
}
