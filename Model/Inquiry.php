<?php


namespace TddWizard\ExerciseContact\Model;

use TddWizard\ExerciseContact\Api\Data\InquiryInterface;

class Inquiry extends \Magento\Framework\Model\AbstractModel implements InquiryInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('TddWizard\ExerciseContact\Model\ResourceModel\Inquiry');
    }

    /**
     * Get inquiry_id
     * @return string
     */
    public function getInquiryId()
    {
        return $this->getData(self::INQUIRY_ID);
    }

    /**
     * Set inquiry_id
     * @param string $inquiryId
     * @return TddWizard\ExerciseContact\Api\Data\InquiryInterface
     */
    public function setInquiryId($inquiryId)
    {
        return $this->setData(self::INQUIRY_ID, $inquiryId);
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return TddWizard\ExerciseContact\Api\Data\InquiryInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get message
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return TddWizard\ExerciseContact\Api\Data\InquiryInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
