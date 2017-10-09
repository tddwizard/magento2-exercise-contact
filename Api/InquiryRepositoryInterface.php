<?php


namespace TddWizard\ExerciseContact\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use TddWizard\ExerciseContact\Api\Data\InquiryInterface;

interface InquiryRepositoryInterface
{


    /**
     * Save Inquiry
     * @param \TddWizard\ExerciseContact\Api\Data\InquiryInterface $inquiry
     * @return \TddWizard\ExerciseContact\Api\Data\InquiryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \TddWizard\ExerciseContact\Api\Data\InquiryInterface $inquiry
    );

    /**
     * Retrieve Inquiry
     * @param string $inquiryId
     * @return \TddWizard\ExerciseContact\Api\Data\InquiryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($inquiryId);

    /**
     * Retrieve Inquiries by email
     *
     * @param string $email
     * @return InquiryInterface[]
     */
    public function getByEmail($email);

    /**
     * Retrieve Inquiry matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \TddWizard\ExerciseContact\Api\Data\InquirySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Inquiry
     * @param \TddWizard\ExerciseContact\Api\Data\InquiryInterface $inquiry
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \TddWizard\ExerciseContact\Api\Data\InquiryInterface $inquiry
    );

    /**
     * Delete Inquiry by ID
     * @param string $inquiryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($inquiryId);
}
