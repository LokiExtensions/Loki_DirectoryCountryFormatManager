<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Controller\Adminhtml\Index;

use Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait\CreateCountryFormat;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\TestCase\AbstractBackendController;

#[DbIsolation(true)]
#[AppArea('adminhtml')]
final class GridTest extends AbstractBackendController
{
    use CreateCountryFormat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Loki_DirectoryCountryFormatManager::grid';
        $this->uri = 'backend/directory_country_format_manager/index/grid';
    }

    public function testGridPageRenders(): void
    {
        $this->dispatch($this->uri);

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = (string)$this->getResponse()->getBody();
        $this->assertStringContainsString('Directory Country Format Manager', $body);
        $this->assertStringContainsString('data-role="grid"', $body);
        $this->assertStringContainsString('Add New', $body);
    }

    public function testGridShowsCountryFormatColumns(): void
    {
        $this->createCountryFormat('NL', 'html', 'LOKI-GRID-COLUMNS');

        $this->dispatch($this->uri);
        $body = (string)$this->getResponse()->getBody();

        preg_match_all('#<th[^>]+data-column="([^"]+)"#', $body, $matches);
        $this->assertSame(['country_format_id', 'country_id', 'type', 'format'], $matches[1]);
    }

    public function testGridListsCountryFormat(): void
    {
        $countryFormat = $this->createCountryFormat('BE', 'oneline', 'LOKI-GRID-ROW {{var city}}');

        $this->dispatch($this->uri);
        $body = (string)$this->getResponse()->getBody();

        $row = $this->getRowHtml($body, 'LOKI-GRID-ROW');
        $this->assertStringContainsString('data-row-id="' . $countryFormat->getId() . '"', $row);
        $this->assertStringContainsString('BE', $row);
        $this->assertStringContainsString('oneline', $row);
    }

    private function getRowHtml(string $body, string $needle): string
    {
        preg_match_all('#<tr class="data-row[^"]*">(.*?)</tr>#s', $body, $matches);
        foreach ($matches[1] as $row) {
            if (str_contains($row, $needle)) {
                return $row;
            }
        }

        $this->fail('No grid row found containing "' . $needle . '"');
    }
}
