<?php declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Form;

use Loki\AdminComponents\Form\Item\ItemValidatorInterface;
use Loki\DirectoryCountryFormatManager\ViewModel\CountryName;
use Loki\DirectoryCountryFormatManager\ViewModel\Options\AddressTemplateTypeOptions;
use Magento\Directory\Model\ResourceModel\Country\Format\CollectionFactory;
use Magento\Framework\DataObject;

class CountryFormatValidator implements ItemValidatorInterface
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly AddressTemplateTypeOptions $addressTemplateTypeOptions,
        private readonly CountryName $countryName,
    ) {
    }

    public function validate(DataObject $item): true|array
    {
        $countryId = (string)$item->getData('country_id');
        $type = (string)$item->getData('type');
        $errors = [];

        if ($countryId === '') {
            $errors[] = (string)__('Please select a country.');
        }

        if ($type === '') {
            $errors[] = (string)__('Please select a type.');
        } elseif (!in_array($type, array_column($this->addressTemplateTypeOptions->toOptionArray(), 'value'), true)) {
            $errors[] = (string)__('The type "%1" is not a valid address template type.', $type);
        }

        if (empty($errors) && $this->isDuplicate($item, $countryId, $type)) {
            $errors[] = (string)__(
                'A country format of type "%1" already exists for the country "%2".',
                $this->addressTemplateTypeOptions->getLabel($type),
                $this->countryName->getName($countryId)
            );
        }

        return empty($errors) ? true : $errors;
    }

    private function isDuplicate(DataObject $item, string $countryId, string $type): bool
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('country_id', $countryId);
        $collection->addFieldToFilter('type', $type);

        $id = (int)$item->getData('country_format_id');
        if ($id > 0) {
            $collection->addFieldToFilter('country_format_id', ['neq' => $id]);
        }

        return $collection->getSize() > 0;
    }
}
