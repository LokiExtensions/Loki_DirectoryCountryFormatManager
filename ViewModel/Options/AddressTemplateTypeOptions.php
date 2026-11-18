<?php declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\ViewModel\Options;

use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AddressTemplateTypeOptions implements ArgumentInterface, OptionSourceInterface
{
    public function __construct(
        private readonly AddressConfig $addressConfig,
    ) {
    }

    public function toOptionArray(): array
    {
        $options = [];
        foreach ($this->addressConfig->getFormats() as $format) {
            $code = (string)$format->getCode();
            $options[] = [
                'value' => $code,
                'label' => (string)__((string)$format->getTitle() ?: $code),
            ];
        }

        return $options;
    }

    public function getLabel(string $code): string
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] === $code) {
                return $option['label'];
            }
        }

        return $code;
    }

    public function getDefaultFormat(string $code): string
    {
        foreach ($this->addressConfig->getFormats() as $format) {
            if ((string)$format->getCode() === $code) {
                return (string)$format->getDefaultFormat();
            }
        }

        return '';
    }
}
