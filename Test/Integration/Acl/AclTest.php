<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Acl;

use Magento\Backend\Model\Menu\Config as MenuConfig;
use Magento\Framework\Acl\AclResource\ProviderInterface as AclResourceProvider;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
final class AclTest extends TestCase
{
    public function testAclResourcesExist(): void
    {
        $aclBuilder = Bootstrap::getObjectManager()->get(AclBuilder::class);
        $aclBuilder->resetRuntimeAcl();
        $acl = $aclBuilder->getAcl();

        $parentResource = 'Loki_DirectoryCountryFormatManager::country_formats';
        $this->assertTrue($acl->hasResource($parentResource));
        $this->assertTrue($acl->inheritsResource($parentResource, 'Magento_Backend::stores_settings', true));

        $aclResources = [
            'Loki_DirectoryCountryFormatManager::grid',
            'Loki_DirectoryCountryFormatManager::form',
            'Loki_DirectoryCountryFormatManager::save',
            'Loki_DirectoryCountryFormatManager::delete',
        ];

        foreach ($aclResources as $aclResource) {
            $this->assertTrue($acl->hasResource($aclResource), 'ACL resource "' . $aclResource . '" not found');
            $this->assertTrue($acl->inheritsResource($aclResource, $parentResource, true));
        }
    }

    public function testAclResourcesHaveReadableTitles(): void
    {
        $resources = Bootstrap::getObjectManager()->get(AclResourceProvider::class)->getAclResources();
        $titles = $this->collectTitles($resources);

        $this->assertSame('Country Format Manager', $titles['Loki_DirectoryCountryFormatManager::country_formats']);
        $this->assertSame('View Country Formats', $titles['Loki_DirectoryCountryFormatManager::grid']);
        $this->assertSame('Open Country Format Form', $titles['Loki_DirectoryCountryFormatManager::form']);
        $this->assertSame('Save Country Formats', $titles['Loki_DirectoryCountryFormatManager::save']);
        $this->assertSame('Delete Country Formats', $titles['Loki_DirectoryCountryFormatManager::delete']);
    }

    public function testMenuItemIsAddedBelowStoresSettings(): void
    {
        $menu = Bootstrap::getObjectManager()->get(MenuConfig::class)->getMenu();

        $settingsMenu = $menu->get('Magento_Backend::stores_settings');
        $this->assertNotNull($settingsMenu, 'Menu item "Magento_Backend::stores_settings" not found');

        $menuItem = $settingsMenu->getChildren()->get('Loki_DirectoryCountryFormatManager::index');
        $this->assertNotNull($menuItem, 'Menu item not found below "Stores > Settings"');

        $menuItemData = $menuItem->toArray();
        $this->assertSame('Country Format Manager', $menuItemData['title']);
        $this->assertSame('directory_country_format_manager/index/grid', $menuItemData['action']);
        $this->assertSame('Loki_DirectoryCountryFormatManager::grid', $menuItemData['resource']);
    }

    private function collectTitles(array $resources): array
    {
        $titles = [];
        foreach ($resources as $resource) {
            $titles[$resource['id']] = (string)($resource['title'] ?? '');
            $titles = array_merge($titles, $this->collectTitles($resource['children'] ?? []));
        }

        return $titles;
    }
}
