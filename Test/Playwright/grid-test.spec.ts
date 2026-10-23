import {test, expect} from '@loki/test';
import config, {themes, withTheme} from './config/config';
import {
    AdminLogin,
    CountryFormatGridPage,
    configureWithTheme,
    createCountryFormat,
    deleteTestCountryFormats,
    getButton,
    uniqueMarker,
} from './helpers/admin-objects';

test.describe('Loki_DirectoryCountryFormatManager grid', () => {
    for (const theme of themes) {
        test(`opens a new form via the "Add New" button (${theme.name})`, async ({page}) => {
            await configureWithTheme(page, withTheme(config, theme));
            await new AdminLogin(page).login();

            const grid = new CountryFormatGridPage(page);
            await grid.open();

            await expect(getButton(page, 'Add New')).toBeVisible();
            await grid.addNew();
            await expect(page).toHaveTitle(/Create Country Format/);
        });

        test(`lists a country format with its columns (${theme.name})`, async ({page}) => {
            await configureWithTheme(page, withTheme(config, theme));
            await new AdminLogin(page).login();
            await deleteTestCountryFormats(page);

            const marker = uniqueMarker('GRID');
            try {
                await createCountryFormat(page, 'DE', 'text', marker + ' {{var city}}', marker);

                // The grid derives its columns from the first item, so they are only shown when there are items
                const grid = new CountryFormatGridPage(page);
                await grid.open();
                await expect(grid.getHeaders()).toHaveText(['ID', 'Country', 'Type', 'Format']);

                const row = grid.getRow(marker);
                await expect(row).toContainText('DE');
                await expect(row).toContainText('text');
                await expect(row.getByRole('link', {name: 'Edit'})).toBeVisible();
            } finally {
                await deleteTestCountryFormats(page);
            }
        });
    }
});
