<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration;

use Magento\Framework\App\Route\ConfigInterface as RouteConfig;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

final class ModuleTest extends TestCase
{
    use AssertModuleIsEnabled;
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;

    public function testIfModuleIsEnabled(): void
    {
        $requiredModules = [
            'Magento_Backend',
            'Magento_Directory',
            'Loki_Components',
            'Loki_AdminComponents',
            'Loki_DirectoryCountryFormatManager',
        ];

        foreach ($requiredModules as $moduleName) {
            $this->assertModuleIsRegistered($moduleName);
            $this->assertModuleIsEnabled($moduleName);
        }
    }

    public function testAdminRouteIsRegistered(): void
    {
        $routeConfig = Bootstrap::getObjectManager()->get(RouteConfig::class);

        $this->assertSame(
            'directory_country_format_manager',
            $routeConfig->getRouteFrontName('directory_country_format_manager', 'adminhtml')
        );
        $this->assertContains(
            'Loki_DirectoryCountryFormatManager',
            $routeConfig->getModulesByFrontName('directory_country_format_manager', 'adminhtml')
        );
    }
}
