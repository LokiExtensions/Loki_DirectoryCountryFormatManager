<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Unit\Controller\Adminhtml\Index;

use Loki\DirectoryCountryFormatManager\Controller\Adminhtml\Index\Grid;

final class GridTest extends AbstractControllerTestCase
{
    public function testAdminResource(): void
    {
        $this->assertSame('Loki_DirectoryCountryFormatManager::grid', Grid::ADMIN_RESOURCE);
    }

    public function testExecuteReturnsConfiguredPage(): void
    {
        $result = (new Grid($this->context, $this->pageFactory))->execute();

        $this->assertSame($this->page, $result);
        $this->assertSame(['Loki_DirectoryCountryFormatManager::index'], $this->activeMenus);
        $this->assertSame(
            [['Directory Country Format Manager', 'Directory Country Format Manager']],
            $this->breadcrumbs
        );
        $this->assertSame(['Directory Country Format Manager'], $this->titles);
    }
}
