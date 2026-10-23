<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Unit\ViewModel\Options;

use Loki\DirectoryCountryFormatManager\ViewModel\Options\AddressTemplateTypeOptions;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use PHPUnit\Framework\TestCase;

final class AddressTemplateTypeOptionsTest extends TestCase
{
    public function testCanBeUsedAsLayoutArgumentAndOptionSource(): void
    {
        $options = new AddressTemplateTypeOptions();

        $this->assertInstanceOf(OptionSourceInterface::class, $options);
        $this->assertInstanceOf(ArgumentInterface::class, $options);
    }

    public function testToOptionArrayReturnsAllAddressTemplateTypes(): void
    {
        $options = (new AddressTemplateTypeOptions())->toOptionArray();

        $this->assertSame(
            [
                ['value' => 'text', 'label' => 'Text'],
                ['value' => 'oneline', 'label' => 'Text One Line'],
                ['value' => 'html', 'label' => 'HTML'],
                ['value' => 'pdf', 'label' => 'PDF'],
            ],
            $options
        );
    }

    public function testToOptionArrayEntriesOnlyContainValueAndLabel(): void
    {
        foreach ((new AddressTemplateTypeOptions())->toOptionArray() as $option) {
            $this->assertSame(['value', 'label'], array_keys($option));
        }
    }

    public function testOptionValuesAreUnique(): void
    {
        $values = array_column((new AddressTemplateTypeOptions())->toOptionArray(), 'value');

        $this->assertSame($values, array_unique($values));
    }
}
