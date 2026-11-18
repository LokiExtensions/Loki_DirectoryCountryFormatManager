<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Unit\ViewModel;

use Loki\DirectoryCountryFormatManager\ViewModel\CountryName;
use Magento\Framework\Locale\ListsInterface;
use PHPUnit\Framework\TestCase;

final class CountryNameTest extends TestCase
{
    public function testGetName(): void
    {
        $localeLists = $this->createMock(ListsInterface::class);
        $localeLists->method('getCountryTranslation')->willReturnCallback(
            fn (string $countryId) => $countryId === 'NL' ? 'Netherlands' : null
        );

        $countryName = new CountryName($localeLists);

        $this->assertSame('Netherlands', $countryName->getName('NL'));
        $this->assertSame('XX', $countryName->getName('XX'));
        $this->assertSame('', $countryName->getName(''));
    }
}
