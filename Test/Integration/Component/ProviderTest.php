<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Component;

use Loki\AdminComponents\ProviderHandler\CollectionHandler;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerListing;
use Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait\CreateCountryFormat;
use Magento\Directory\Model\Country\Format;
use Magento\Directory\Model\ResourceModel\Country\Format as FormatResource;
use Magento\Directory\Model\ResourceModel\Country\Format\Collection as FormatCollection;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verify that the provider configured in the layout can be used by Loki_AdminComponents to read and write
 * the core table `directory_country_format`
 */
#[DbIsolation(true)]
#[AppArea('adminhtml')]
final class ProviderTest extends TestCase
{
    use CreateCountryFormat;

    #[DataProvider('layoutProvider')]
    public function testBlockArgumentsPointToCountryFormatClasses(string $handle, string $blockName): void
    {
        $block = $this->getBlock($handle, $blockName);

        $this->assertSame(FormatCollection::class, $block->getData('provider'));
        $this->assertSame(FormatResource::class, $block->getData('resource_model'));
        $this->assertSame('directory_country_format_manager/index/grid', $block->getData('index_url'));
    }

    public static function layoutProvider(): array
    {
        return [
            'grid' => ['directory_country_format_manager_index_grid', 'directory_country_format_manager.grid'],
            'form' => ['directory_country_format_manager_index_form', 'directory_country_format_manager.form'],
        ];
    }

    public function testFormFieldsUseModuleOptions(): void
    {
        $block = $this->getBlock('directory_country_format_manager_index_form', 'directory_country_format_manager.form');
        $fields = (array)$block->getData('fields');

        $this->assertSame(['country_id', 'type', 'format'], array_keys($fields));
        $this->assertSame('select', $fields['type']['field_type']);
        $this->assertSame(
            ['text', 'oneline', 'html', 'pdf'],
            array_column($fields['type']['options']->toOptionArray(), 'value')
        );
        $this->assertSame('textarea', $fields['format']['field_type']);
        $this->assertTrue($fields['format']['allow_html']);
    }

    public function testProviderHandlerSavesAndLoadsCountryFormat(): void
    {
        $provider = Bootstrap::getObjectManager()->create(FormatCollection::class);
        $providerHandler = Bootstrap::getObjectManager()->get(ProviderHandlerListing::class)->getByProvider($provider);
        $this->assertInstanceOf(CollectionHandler::class, $providerHandler);
        $this->assertSame(FormatResource::class, $providerHandler->getResourceModelClass($provider));

        $item = $providerHandler->createItem($provider, null);
        $this->assertInstanceOf(Format::class, $item);

        $item->setData(['country_id' => 'NL', 'type' => 'html', 'format' => 'LOKI-PROVIDER-SAVE']);
        $providerHandler->saveItem($provider, $item);
        $this->assertGreaterThan(0, (int)$item->getId());

        $loadedItem = $providerHandler->getItem(
            Bootstrap::getObjectManager()->create(FormatCollection::class),
            (int)$item->getId()
        );
        $this->assertSame('NL', $loadedItem->getData('country_id'));
        $this->assertSame('html', $loadedItem->getData('type'));
        $this->assertSame('LOKI-PROVIDER-SAVE', $loadedItem->getData('format'));
    }

    public function testProviderHandlerUpdatesAndDeletesCountryFormat(): void
    {
        $countryFormat = $this->createCountryFormat('BE', 'text', 'LOKI-PROVIDER-ORIGINAL');
        $provider = Bootstrap::getObjectManager()->create(FormatCollection::class);
        $providerHandler = Bootstrap::getObjectManager()->get(ProviderHandlerListing::class)->getByProvider($provider);

        $item = $providerHandler->getItem($provider, (int)$countryFormat->getId());
        $item->setData('format', 'LOKI-PROVIDER-UPDATED');
        $providerHandler->saveItem($provider, $item);

        $this->assertSame(
            'LOKI-PROVIDER-UPDATED',
            $this->loadCountryFormat((int)$countryFormat->getId())->getData('format')
        );

        $providerHandler->deleteItem($provider, $item);
        $this->assertNull($this->loadCountryFormat((int)$countryFormat->getId())->getId());
    }

    private function getBlock(string $handle, string $blockName): AbstractBlock
    {
        // Build the layout like an admin page request does, so that the page layout and its containers are included
        $page = Bootstrap::getObjectManager()->get(PageFactory::class)->create();
        $page->addHandle($handle);

        $block = $page->getLayout()->getBlock($blockName);
        $this->assertInstanceOf(AbstractBlock::class, $block, 'Block "' . $blockName . '" not found');

        return $block;
    }
}
