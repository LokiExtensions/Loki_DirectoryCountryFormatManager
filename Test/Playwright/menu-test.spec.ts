import {test, expect} from '@loki/test';
import config, {themes, withTheme} from './config/config';
import {AdminLogin, CountryFormatGridPage, GRID_PATH, configureWithTheme} from './helpers/admin-objects';

test.describe('Loki_DirectoryCountryFormatManager admin menu', () => {
    for (const theme of themes) {
        test(`opens the grid via Stores > Settings > Country Format Manager (${theme.name})`, async ({page}) => {
            await configureWithTheme(page, withTheme(config, theme));
            await new AdminLogin(page).login();

            const menuLink = page.locator('[data-ui-id="menu-loki-directorycountryformatmanager-index"] a');
            await expect(menuLink).toBeAttached();
            await expect(menuLink).toHaveAttribute('href', new RegExp(GRID_PATH));
            await expect(menuLink).toContainText('Country Format Manager');

            await page.goto((await menuLink.getAttribute('href'))!);
            await new CountryFormatGridPage(page).expectToBeLoaded();
        });
    }
});
