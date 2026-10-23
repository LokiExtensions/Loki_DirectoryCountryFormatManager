<?php

declare(strict_types=1);

namespace Loki\DirectoryCountryFormatManager\Test\Integration\Acl;

use Magento\Backend\Model\Menu\Config as MenuConfig;
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

        $aclResources = [
            'Loki_DirectoryCountryFormatManager::grid',
            'Loki_DirectoryCountryFormatManager::form',
        ];

        foreach ($aclResources as $aclResource) {
            $this->assertTrue($acl->hasResource($aclResource), 'ACL resource "' . $aclResource . '" not found');
            $this->assertTrue($acl->inheritsResource($aclResource, 'Magento_Backend::admin'));
        }
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
}
