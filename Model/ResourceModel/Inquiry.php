<?php


namespace TddWizard\ExerciseContact\Model\ResourceModel;

class Inquiry extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tddwizard_inquiry', 'inquiry_id');
    }
}
