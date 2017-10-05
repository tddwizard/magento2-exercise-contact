<?php


namespace TddWizard\ExerciseContact\Controller\Adminhtml\Inquiry;

class Delete extends \TddWizard\ExerciseContact\Controller\Adminhtml\Inquiry
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('inquiry_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('TddWizard\ExerciseContact\Model\Inquiry');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the Inquiry.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['inquiry_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Inquiry to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
