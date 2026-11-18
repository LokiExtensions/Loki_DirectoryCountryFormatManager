<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Unit\Form;

use Loki\DirectoryCountryFormatManager\Form\CountryFormatConvertor;
use Loki\DirectoryCountryFormatManager\ViewModel\Options\AddressTemplateTypeOptions;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;

final class CountryFormatConvertorTest extends TestCase
{
    public function testEmptyFormatIsFilledWithDefaultFormatOfType(): void
    {
        $item = new DataObject(['type' => 'html', 'format' => " \n"]);

        $this->createConvertor()->beforeSave($item);

        $this->assertSame('DEFAULT-html', $item->getData('format'));
    }

    public function testExistingFormatIsKept(): void
    {
        $item = new DataObject(['type' => 'html', 'format' => '{{var city}}']);

        $this->createConvertor()->beforeSave($item);

        $this->assertSame('{{var city}}', $item->getData('format'));
    }

    public function testAfterLoadKeepsItem(): void
    {
        $item = new DataObject(['format' => '']);

        $this->assertSame($item, $this->createConvertor()->afterLoad($item));
        $this->assertSame('', $item->getData('format'));
    }

    private function createConvertor(): CountryFormatConvertor
    {
        $options = $this->createMock(AddressTemplateTypeOptions::class);
        $options->method('getDefaultFormat')->willReturnCallback(fn (string $code) => 'DEFAULT-' . $code);

        return new CountryFormatConvertor($options);
    }
}
