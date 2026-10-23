<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Component;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\Components\Config\XmlConfig;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
final class ComponentsTest extends TestCase
{
    #[DataProvider('componentProvider')]
    public function testComponentIsDefined(string $name, string $viewModelClass, string $repositoryClass): void
    {
        $definitions = Bootstrap::getObjectManager()->get(XmlConfig::class)->getComponentDefinitions();

        $this->assertArrayHasKey($name, $definitions, 'Component "' . $name . '" not found');
        $this->assertSame($viewModelClass, $definitions[$name]->getViewModelClass());
        $this->assertSame($repositoryClass, $definitions[$name]->getRepositoryClass());
    }

    public static function componentProvider(): array
    {
        return [
            'grid' => ['directory_country_format_manager.grid', GridViewModel::class, GridRepository::class],
            'form' => ['directory_country_format_manager.form', FormViewModel::class, FormRepository::class],
        ];
    }
}
