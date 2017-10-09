<?php


namespace TddWizard\ExerciseContact\Controller\Adminhtml;

abstract class Inquiry extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'TddWizard_ExerciseContact::Inquiry';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu('TddWizard::top_level')
            ->addBreadcrumb(__('TddWizard'), __('TddWizard'))
            ->addBreadcrumb(__('Inquiry'), __('Inquiry'));
        return $resultPage;
    }
}
