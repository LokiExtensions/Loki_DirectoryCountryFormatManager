<?php declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\ViewModel;

use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CountryName implements ArgumentInterface
{
    public function __construct(
        private readonly ListsInterface $localeLists,
    ) {
    }

    public function getName(string $countryId): string
    {
        if ($countryId === '') {
            return '';
        }

        $name = (string)$this->localeLists->getCountryTranslation($countryId);

        return $name !== '' ? $name : $countryId;
    }
}
