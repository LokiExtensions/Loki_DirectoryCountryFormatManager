<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Model;

use Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait\CreateCountryFormat;
use Magento\Customer\Block\Address\Renderer\RendererInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Directory\Model\Country;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Verify that a format stored in `directory_country_format` (as managed by this module) is picked up by
 * the Magento address renderers
 */
#[DbIsolation(true)]
#[AppArea('frontend')]
final class CountryFormatUsageTest extends TestCase
{
    use CreateCountryFormat;

    private const ADDRESS = [
        'firstname' => 'Loki',
        'lastname' => 'Tester',
        'street' => 'Main Street 1',
        'postcode' => '1234 AB',
        'city' => 'Amsterdam',
        'telephone' => '0201234567',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        // Country formats are cached statically per country, so reset them to pick up the fixtures of each test
        Country::$_format = [];
    }

    protected function tearDown(): void
    {
        Country::$_format = [];
        parent::tearDown();
    }

    public function testCountryFormatIsUsedForMatchingCountryAndType(): void
    {
        $this->createCountryFormat('NL', 'html', 'LOKI-HTML {{var city}}|{{var postcode}}');

        $output = $this->getRenderer('html')->renderArray(self::ADDRESS + ['country_id' => 'NL']);

        $this->assertStringContainsString('LOKI-HTML Amsterdam|1234 AB', (string)$output);
    }

    public function testCountryFormatIsNotUsedForOtherType(): void
    {
        $this->createCountryFormat('NL', 'html', 'LOKI-HTML {{var city}}');

        $output = $this->getRenderer('text')->renderArray(self::ADDRESS + ['country_id' => 'NL']);

        $this->assertStringNotContainsString('LOKI-HTML', (string)$output);
        $this->assertStringContainsString('Amsterdam', (string)$output);
    }

    public function testCountryFormatIsNotUsedForOtherCountry(): void
    {
        $this->createCountryFormat('NL', 'html', 'LOKI-HTML {{var city}}');

        $output = $this->getRenderer('html')->renderArray(self::ADDRESS + ['country_id' => 'BE']);

        $this->assertStringNotContainsString('LOKI-HTML', (string)$output);
        $this->assertStringContainsString('Amsterdam', (string)$output);
    }

    public function testDefaultFormatIsUsedWithoutCountryFormat(): void
    {
        $output = $this->getRenderer('html')->renderArray(self::ADDRESS + ['country_id' => 'NL']);

        $this->assertStringNotContainsString('LOKI-HTML', (string)$output);
        $this->assertStringContainsString('Loki', (string)$output);
        $this->assertStringContainsString('Amsterdam', (string)$output);
    }

    private function getRenderer(string $type): RendererInterface
    {
        $addressConfig = Bootstrap::getObjectManager()->create(AddressConfig::class);
        $renderer = $addressConfig->getFormatByCode($type)->getRenderer();
        $this->assertInstanceOf(RendererInterface::class, $renderer);

        return $renderer;
    }
}
