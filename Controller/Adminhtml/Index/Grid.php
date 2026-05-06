<?php
declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Grid extends Action
{
    const ADMIN_RESOURCE = 'Loki_DirectoryCountryFormatManager::grid';

    public function __construct(
        Context $context,
        private ResultPageFactory $resultPageFactory,
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $title = 'Directory Country Format Manager';

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Loki_DirectoryCountryFormatManager::index');
        $resultPage->addBreadcrumb(__($title), __($title));
        $resultPage->getConfig()->getTitle()->prepend(__($title));

        return $resultPage;
    }
}
