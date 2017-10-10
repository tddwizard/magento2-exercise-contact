<?php

namespace TddWizard\ExerciseContact\Test\Unit\Fake;

use TddWizard\ExerciseContact\Api\Data\InquiryInterface;
use TddWizard\ExerciseContact\Api\InquiryRepositoryInterface;

/**
 * Partial fake implementation of inquiry repository
 */
class InquiryMemoryRepository implements InquiryRepositoryInterface
{
    public $inquiries = [];

    public function save(InquiryInterface $inquiry)
    {
        $this->inquiries[] = $inquiry;
    }

    public function getById($inquiryId)
    {
    }

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
    }

    public function delete(
        \TddWizard\ExerciseContact\Api\Data\InquiryInterface $inquiry)
    {
    }

    public function deleteById($inquiryId)
    {
    }

    public function getByEmail($email)
    {
    }

}