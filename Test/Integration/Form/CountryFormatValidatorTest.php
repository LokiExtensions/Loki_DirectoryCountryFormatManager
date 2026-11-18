<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Form;

use Loki\DirectoryCountryFormatManager\Form\CountryFormatValidator;
use Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait\CreateCountryFormat;
use Magento\Framework\DataObject;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
#[DbIsolation(true)]
final class CountryFormatValidatorTest extends TestCase
{
    use CreateCountryFormat;

    public function testValidCountryFormatIsAccepted(): void
    {
        $this->assertTrue($this->validate(['country_id' => 'NL', 'type' => 'html']));
    }

    public function testMissingCountryAndTypeAreRejected(): void
    {
        $this->assertSame(
            ['Please select a country.', 'Please select a type.'],
            $this->validate(['country_id' => '', 'type' => ''])
        );
    }

    public function testUnknownTypeIsRejected(): void
    {
        $this->assertSame(
            ['The type "unknown" is not a valid address template type.'],
            $this->validate(['country_id' => 'NL', 'type' => 'unknown'])
        );
    }

    public function testDuplicateCountryAndTypeIsRejected(): void
    {
        $this->createCountryFormat('NL', 'html', 'LOKI-VALIDATOR');

        $this->assertSame(
            ['A country format of type "HTML" already exists for the country "Netherlands".'],
            $this->validate(['country_id' => 'NL', 'type' => 'html'])
        );
    }

    public function testSameCountryFormatIsNotADuplicateOfItself(): void
    {
        $countryFormat = $this->createCountryFormat('NL', 'html', 'LOKI-VALIDATOR');

        $this->assertTrue($this->validate([
            'country_format_id' => $countryFormat->getId(),
            'country_id' => 'NL',
            'type' => 'html',
        ]));
    }

    public function testOtherTypeOfSameCountryIsAccepted(): void
    {
        $this->createCountryFormat('NL', 'html', 'LOKI-VALIDATOR');

        $this->assertTrue($this->validate(['country_id' => 'NL', 'type' => 'pdf']));
    }

    private function validate(array $data): true|array
    {
        return Bootstrap::getObjectManager()->create(CountryFormatValidator::class)->validate(new DataObject($data));
    }
}
