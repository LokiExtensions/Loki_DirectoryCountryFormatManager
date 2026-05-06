<?php
declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Form extends Action
{
    const ADMIN_RESOURCE = 'Loki_DirectoryCountryFormatManager::form';

    public function __construct(
        Context $context,
        private ResultPageFactory $resultPageFactory,
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $title = __('Create Country Format');
        if ($this->getRequest()->getParam('id')) {
            $title = __('Edit Country Format');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Loki_DirectoryCountryFormatManager::index');
        $resultPage->addBreadcrumb($title, $title);
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
