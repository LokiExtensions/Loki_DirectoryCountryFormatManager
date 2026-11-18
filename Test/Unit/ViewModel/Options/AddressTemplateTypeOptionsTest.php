<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Unit\ViewModel\Options;

use Loki\DirectoryCountryFormatManager\ViewModel\Options\AddressTemplateTypeOptions;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use PHPUnit\Framework\TestCase;

final class AddressTemplateTypeOptionsTest extends TestCase
{
    public function testCanBeUsedAsLayoutArgumentAndOptionSource(): void
    {
        $options = $this->createOptions([]);

        $this->assertInstanceOf(OptionSourceInterface::class, $options);
        $this->assertInstanceOf(ArgumentInterface::class, $options);
    }

    public function testToOptionArrayReturnsFormatsFromAddressConfig(): void
    {
        $options = $this->createOptions([
            ['code' => 'text', 'title' => 'Text'],
            ['code' => 'oneline', 'title' => 'Text One Line'],
            ['code' => 'html', 'title' => 'HTML'],
            ['code' => 'pdf', 'title' => 'PDF'],
            ['code' => 'custom', 'title' => 'Custom Format'],
        ])->toOptionArray();

        $this->assertSame(
            [
                ['value' => 'text', 'label' => 'Text'],
                ['value' => 'oneline', 'label' => 'Text One Line'],
                ['value' => 'html', 'label' => 'HTML'],
                ['value' => 'pdf', 'label' => 'PDF'],
                ['value' => 'custom', 'label' => 'Custom Format'],
            ],
            $options
        );
    }

    public function testCodeIsUsedAsLabelWhenTitleIsMissing(): void
    {
        $options = $this->createOptions([['code' => 'custom', 'title' => '']])->toOptionArray();

        $this->assertSame([['value' => 'custom', 'label' => 'custom']], $options);
    }

    public function testGetLabel(): void
    {
        $options = $this->createOptions([['code' => 'oneline', 'title' => 'Text One Line']]);

        $this->assertSame('Text One Line', $options->getLabel('oneline'));
        $this->assertSame('unknown', $options->getLabel('unknown'));
    }

    public function testGetDefaultFormat(): void
    {
        $options = $this->createOptions([['code' => 'html', 'title' => 'HTML', 'default_format' => '{{var city}}']]);

        $this->assertSame('{{var city}}', $options->getDefaultFormat('html'));
        $this->assertSame('', $options->getDefaultFormat('unknown'));
    }

    private function createOptions(array $formats): AddressTemplateTypeOptions
    {
        $addressConfig = $this->createMock(AddressConfig::class);
        $addressConfig->method('getFormats')->willReturn(
            array_map(fn (array $format) => new DataObject($format), $formats)
        );

        return new AddressTemplateTypeOptions($addressConfig);
    }
}
