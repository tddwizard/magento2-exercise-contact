<?php

namespace TddWizard\ExerciseContact\Model;

/**
 * @property \Magento\Framework\Session\Storage $storage
 */
class Session extends \Magento\Framework\Session\Generic
{
    const KEY_FORM_DATA = 'inquiry_form';

    public function saveFormData(array $values)
    {
        $this->storage->setData(self::KEY_FORM_DATA, $values);
    }

    public function getSavedFormData() : array
    {
        return (array)$this->getData(self::KEY_FORM_DATA, true) ?: [];
    }
}