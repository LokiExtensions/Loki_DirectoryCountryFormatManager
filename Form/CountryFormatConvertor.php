<?php declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Form;

use Loki\AdminComponents\Form\Item\ItemConvertorInterface;
use Loki\DirectoryCountryFormatManager\ViewModel\Options\AddressTemplateTypeOptions;
use Magento\Framework\DataObject;

class CountryFormatConvertor implements ItemConvertorInterface
{
    public function __construct(
        private readonly AddressTemplateTypeOptions $addressTemplateTypeOptions,
    ) {
    }

    public function afterLoad(DataObject $item): DataObject
    {
        return $item;
    }

    public function beforeSave(DataObject $item): DataObject
    {
        if (trim((string)$item->getData('format'), " \f\n\r\t\v\x00") !== '') {
            return $item;
        }

        $item->setData('format', $this->addressTemplateTypeOptions->getDefaultFormat((string)$item->getData('type')));

        return $item;
    }
}
