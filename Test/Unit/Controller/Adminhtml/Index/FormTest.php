<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Unit\Controller\Adminhtml\Index;

use Loki\DirectoryCountryFormatManager\Controller\Adminhtml\Index\Form;
use PHPUnit\Framework\Attributes\DataProvider;

final class FormTest extends AbstractControllerTestCase
{
    public function testAdminResource(): void
    {
        $this->assertSame('Loki_DirectoryCountryFormatManager::form', Form::ADMIN_RESOURCE);
    }

    #[DataProvider('newItemIdProvider')]
    public function testNewItemRendersCreatePage(mixed $id): void
    {
        $this->requestParams = ['id' => $id];

        $result = $this->createController()->execute();

        $this->assertSame($this->page, $result);
        $this->assertSame(['Create Country Format'], $this->titles);
        $this->assertSame([['Create Country Format', 'Create Country Format']], $this->breadcrumbs);
        $this->assertSame(['Loki_DirectoryCountryFormatManager::index'], $this->activeMenus);
    }

    public static function newItemIdProvider(): array
    {
        return [
            'missing' => [null],
            'empty' => [''],
            'zero' => ['0'],
        ];
    }

    public function testExistingItemRendersEditPage(): void
    {
        $this->requestParams = ['id' => '3'];

        $result = $this->createController()->execute();

        $this->assertSame($this->page, $result);
        $this->assertSame(['Edit Country Format'], $this->titles);
        $this->assertSame([['Edit Country Format', 'Edit Country Format']], $this->breadcrumbs);
        $this->assertSame(['Loki_DirectoryCountryFormatManager::index'], $this->activeMenus);
    }

    private function createController(): Form
    {
        return new Form($this->context, $this->pageFactory);
    }
}
