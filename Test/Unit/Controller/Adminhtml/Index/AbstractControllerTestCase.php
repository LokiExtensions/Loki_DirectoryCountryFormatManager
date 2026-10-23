<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Unit\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractControllerTestCase extends TestCase
{
    protected RequestInterface&MockObject $request;
    protected Context&MockObject $context;
    protected Page&MockObject $page;
    protected PageFactory&MockObject $pageFactory;
    protected array $requestParams = [];
    protected array $activeMenus = [];
    protected array $breadcrumbs = [];
    protected array $titles = [];

    protected function setUp(): void
    {
        $this->request = $this->createMock(RequestInterface::class);
        $this->request->method('getParam')->willReturnCallback(
            fn (string $name, mixed $default = null) => $this->requestParams[$name] ?? $default
        );

        $this->context = $this->createMock(Context::class);
        $this->context->method('getRequest')->willReturn($this->request);

        $title = $this->createMock(Title::class);
        $title->method('prepend')->willReturnCallback(
            function ($value) {
                $this->titles[] = (string)$value;
            }
        );

        $pageConfig = $this->createMock(Config::class);
        $pageConfig->method('getTitle')->willReturn($title);

        $this->page = $this->createMock(Page::class);
        $this->page->method('getConfig')->willReturn($pageConfig);
        $this->page->method('setActiveMenu')->willReturnCallback(
            function (string $menu) {
                $this->activeMenus[] = $menu;
                return $this->page;
            }
        );
        $this->page->method('addBreadcrumb')->willReturnCallback(
            function ($label, $title) {
                $this->breadcrumbs[] = [(string)$label, (string)$title];
                return $this->page;
            }
        );

        $this->pageFactory = $this->createMock(PageFactory::class);
        $this->pageFactory->method('create')->willReturn($this->page);
    }
}
