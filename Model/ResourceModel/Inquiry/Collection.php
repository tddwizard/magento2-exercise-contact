<?php


namespace TddWizard\ExerciseContact\Model\ResourceModel\Inquiry;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'TddWizard\ExerciseContact\Model\Inquiry',
            'TddWizard\ExerciseContact\Model\ResourceModel\Inquiry'
        );
    }
}
