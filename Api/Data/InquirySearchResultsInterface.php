<?php


namespace TddWizard\ExerciseContact\Api\Data;

interface InquirySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Inquiry list.
     * @return \TddWizard\ExerciseContact\Api\Data\InquiryInterface[]
     */
    
    public function getItems();

    /**
     * Set email list.
     * @param \TddWizard\ExerciseContact\Api\Data\InquiryInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
