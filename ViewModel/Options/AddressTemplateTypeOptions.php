<?php declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\ViewModel\Options;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AddressTemplateTypeOptions implements ArgumentInterface, OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];

        $options[] = [
            'value' => 'text',
            'label' => 'Text',
        ];

        $options[] = [
            'value' => 'oneline',
            'label' => 'Text One Line',
        ];

        $options[] = [
            'value' => 'html',
            'label' => 'HTML',
        ];

        $options[] = [
            'value' => 'pdf',
            'label' => 'PDF',
        ];

        return $options;
    }
}
