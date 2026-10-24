<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Controller\Adminhtml\Index;

use Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait\CreateCountryFormat;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\TestCase\AbstractBackendController;

#[DbIsolation(true)]
#[AppArea('adminhtml')]
final class FormTest extends AbstractBackendController
{
    use CreateCountryFormat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Loki_DirectoryCountryFormatManager::form';
        $this->uri = 'backend/directory_country_format_manager/index/form';
    }

    public function testNewFormRendersFields(): void
    {
        $this->dispatch($this->uri);

        /** @var HttpResponse $response */
        $response = $this->getResponse();
        $this->assertSame(200, $response->getHttpResponseCode());

        $body = (string)$response->getBody();
        $this->assertStringContainsString('Create Country Format', $body);
        $this->assertStringNotContainsString('Edit Country Format', $body);

        $this->assertMatchesRegularExpression('#<select[^>]+data-name="country_id"#s', $body);
        $this->assertMatchesRegularExpression('#<select[^>]+data-name="type"#s', $body);
        $this->assertMatchesRegularExpression('#<textarea[^>]+data-name="format"[^>]+rows="20"#s', $body);

        $typeSelect = $this->getSelectHtml($body, 'type');
        foreach (['text', 'oneline', 'html', 'pdf'] as $type) {
            $this->assertStringContainsString('value="' . $type . '"', $typeSelect);
        }

        $countrySelect = $this->getSelectHtml($body, 'country_id');
        $this->assertStringContainsString('value="NL"', $countrySelect);
        $this->assertStringContainsString('value="US"', $countrySelect);
    }

    public function testEditFormRendersExistingCountryFormat(): void
    {
        $countryFormat = $this->createCountryFormat('NL', 'html', 'LOKI-FORM-EDIT {{var city}}');

        $this->dispatch($this->uri . '/id/' . $countryFormat->getId());

        /** @var HttpResponse $response */
        $response = $this->getResponse();
        $this->assertSame(200, $response->getHttpResponseCode());

        $body = (string)$response->getBody();
        $this->assertStringContainsString('Edit Country Format', $body);
        $this->assertStringContainsString('LOKI-FORM-EDIT', $body);
    }

    private function getSelectHtml(string $body, string $name): string
    {
        $this->assertSame(
            1,
            preg_match('#<select[^>]+data-name="' . $name . '".*?</select>#s', $body, $matches),
            'No select found for "' . $name . '"'
        );

        return $matches[0];
    }
}
