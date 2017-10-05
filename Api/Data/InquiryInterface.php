<?php


namespace TddWizard\ExerciseContact\Api\Data;

interface InquiryInterface
{

    const MESSAGE = 'message';
    const INQUIRY_ID = 'inquiry_id';
    const EMAIL = 'email';


    /**
     * Get inquiry_id
     * @return string|null
     */
    
    public function getInquiryId();

    /**
     * Set inquiry_id
     * @param string $inquiry_id
     * @return TddWizard\ExerciseContact\Api\Data\InquiryInterface
     */
    
    public function setInquiryId($inquiryId);

    /**
     * Get email
     * @return string|null
     */
    
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return TddWizard\ExerciseContact\Api\Data\InquiryInterface
     */
    
    public function setEmail($email);

    /**
     * Get message
     * @return string|null
     */
    
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return TddWizard\ExerciseContact\Api\Data\InquiryInterface
     */
    
    public function setMessage($message);
}
