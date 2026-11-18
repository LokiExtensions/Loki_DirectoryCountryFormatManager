<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Form;

use Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait\CreateCountryFormat;
use Loki\DirectoryCountryFormatManager\Test\Integration\FixtureTrait\PostFormAction;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Directory\Model\ResourceModel\Country\Format\CollectionFactory;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\TestCase\AbstractBackendController;

#[AppArea('adminhtml')]
#[DbIsolation(true)]
final class FormActionTest extends AbstractBackendController
{
    use CreateCountryFormat;
    use PostFormAction;

    protected $resource = 'Loki_DirectoryCountryFormatManager::form';
    protected $uri = 'backend/directory_country_format_manager/index/form';

    public function testCountryFormatIsCreated(): void
    {
        $this->postFormAction('save', ['country_id' => 'NL', 'type' => 'html', 'format' => 'LOKI-CREATE']);

        $this->assertSame(['LOKI-CREATE'], $this->getFormats('NL', 'html'));
    }

    public function testEmptyFormatIsFilledWithDefaultTemplate(): void
    {
        $this->postFormAction('save', ['country_id' => 'NL', 'type' => 'oneline', 'format' => '']);

        $defaultFormat = (string)$this->_objectManager->get(AddressConfig::class)
            ->getFormatByCode('oneline')->getDefaultFormat();
        $this->assertNotSame('', $defaultFormat);
        $this->assertSame([$defaultFormat], $this->getFormats('NL', 'oneline'));
    }

    public function testDuplicateCountryAndTypeIsRejected(): void
    {
        $this->createCountryFormat('NL', 'html', 'LOKI-ORIGINAL');

        $this->postFormAction('save', ['country_id' => 'NL', 'type' => 'html', 'format' => 'LOKI-DUPLICATE']);

        $this->assertSame(['LOKI-ORIGINAL'], $this->getFormats('NL', 'html'));
        $this->assertSessionMessages(
            $this->containsEqual('A country format of type "HTML" already exists for the country "Netherlands".'),
            MessageInterface::TYPE_ERROR
        );
    }

    public function testExistingCountryFormatCanBeSavedAgain(): void
    {
        $countryFormat = $this->createCountryFormat('NL', 'html', 'LOKI-ORIGINAL');

        $this->postFormAction('save', [
            'country_format_id' => $countryFormat->getId(),
            'country_id' => 'NL',
            'type' => 'html',
            'format' => 'LOKI-UPDATED',
        ]);

        $this->assertSame(['LOKI-UPDATED'], $this->getFormats('NL', 'html'));
    }

    public function testMissingCountryAndTypeAreRejected(): void
    {
        $this->postFormAction('save', ['country_id' => '', 'type' => '', 'format' => 'LOKI-MISSING']);

        $collection = $this->_objectManager->create(CollectionFactory::class)->create()
            ->addFieldToFilter('format', 'LOKI-MISSING');
        $this->assertSame(0, $collection->getSize());
        $this->assertSessionMessages(
            $this->logicalAnd(
                $this->containsEqual('Please select a country.'),
                $this->containsEqual('Please select a type.')
            ),
            MessageInterface::TYPE_ERROR
        );
    }

    public function testInvalidTypeIsRejected(): void
    {
        $this->postFormAction('save', ['country_id' => 'NL', 'type' => 'unknown', 'format' => 'LOKI-INVALID']);

        $this->assertSame([], $this->getFormats('NL', 'unknown'));
    }

    public function testCountryFormatIsNotSavedWithoutSavePermission(): void
    {
        $countryFormat = $this->createCountryFormat('NL', 'html', 'LOKI-ORIGINAL');
        $this->deny('Loki_DirectoryCountryFormatManager::save');

        $this->postFormAction('save', [
            'country_format_id' => $countryFormat->getId(),
            'country_id' => 'NL',
            'type' => 'html',
            'format' => 'LOKI-CHANGED',
        ]);

        $this->assertSame(['LOKI-ORIGINAL'], $this->getFormats('NL', 'html'));
    }

    public function testCountryFormatIsDeletedWithDeletePermission(): void
    {
        $countryFormat = $this->createCountryFormat('NL', 'html', 'LOKI-DELETE');

        $this->postFormAction('delete', ['country_format_id' => $countryFormat->getId()]);

        $this->assertNull($this->loadCountryFormat((int)$countryFormat->getId())->getId());
    }

    public function testCountryFormatIsNotDeletedWithoutDeletePermission(): void
    {
        $countryFormat = $this->createCountryFormat('NL', 'html', 'LOKI-DELETE');
        $this->deny('Loki_DirectoryCountryFormatManager::delete');

        $this->postFormAction('delete', ['country_format_id' => $countryFormat->getId()]);

        $this->assertNotNull($this->loadCountryFormat((int)$countryFormat->getId())->getId());
    }

    public function testCountryFormatIsNotDeletedWithoutFormPermission(): void
    {
        $countryFormat = $this->createCountryFormat('NL', 'html', 'LOKI-DELETE');
        $this->deny('Loki_DirectoryCountryFormatManager::form');

        $this->postFormAction('delete', ['country_format_id' => $countryFormat->getId()]);

        $this->assertNotNull($this->loadCountryFormat((int)$countryFormat->getId())->getId());
    }

    private function getFormats(string $countryId, string $type): array
    {
        $collection = $this->_objectManager->create(CollectionFactory::class)->create()
            ->addFieldToFilter('country_id', $countryId)
            ->addFieldToFilter('type', $type);

        return array_values(array_map(fn ($item) => (string)$item->getData('format'), $collection->getItems()));
    }
}
