<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait;

use Magento\Directory\Model\Country\Format;
use Magento\Directory\Model\Country\FormatFactory;
use Magento\Directory\Model\ResourceModel\Country\Format as FormatResource;
use Magento\TestFramework\Helper\Bootstrap;

trait CreateCountryFormat
{
    private function createCountryFormat(string $countryId, string $type, string $format): Format
    {
        /** @var Format $countryFormat */
        $countryFormat = Bootstrap::getObjectManager()->get(FormatFactory::class)->create();
        $countryFormat->setData([
            'country_id' => $countryId,
            'type' => $type,
            'format' => $format,
        ]);

        $this->getCountryFormatResource()->save($countryFormat);

        return $this->loadCountryFormat((int)$countryFormat->getId());
    }

    private function loadCountryFormat(int $countryFormatId): Format
    {
        /** @var Format $countryFormat */
        $countryFormat = Bootstrap::getObjectManager()->get(FormatFactory::class)->create();
        $this->getCountryFormatResource()->load($countryFormat, $countryFormatId);

        return $countryFormat;
    }

    private function getCountryFormatResource(): FormatResource
    {
        return Bootstrap::getObjectManager()->get(FormatResource::class);
    }
}
