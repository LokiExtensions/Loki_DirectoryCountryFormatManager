import {test, expect} from '@loki/test';
import config, {themes, withTheme} from './config/config';
import {
    AdminLogin,
    CountryFormatFormPage,
    CountryFormatGridPage,
    configureWithTheme,
    createCountryFormat,
    deleteTestCountryFormats,
    expectMessage,
    getButton,
    uniqueMarker,
} from './helpers/admin-objects';

test.describe('Loki_DirectoryCountryFormatManager form', () => {
    for (const theme of themes) {
        test(`offers the address template types (${theme.name})`, async ({page}) => {
            await configureWithTheme(page, withTheme(config, theme));
            await new AdminLogin(page).login();

            const form = new CountryFormatFormPage(page);
            await form.open();

            await expect(page).toHaveTitle(/Create Country Format/);
            await expect(form.getTypeField().locator('option:not([value=""])'))
                .toHaveText(['Text', 'Text One Line', 'HTML', 'PDF']);
            await expect(form.getCountryField().locator('option[value="NL"]')).toHaveCount(1);
            await expect(form.getFormatField()).toHaveAttribute('rows', '20');

            await expect(getButton(page, 'Back')).toBeVisible();
            await expect(getButton(page, 'Save & Close')).toBeVisible();
            await expect(getButton(page, 'Delete')).toHaveCount(0);
        });

        test(`creates, edits and deletes a country format (${theme.name})`, async ({page}) => {
            await configureWithTheme(page, withTheme(config, theme));
            await new AdminLogin(page).login();
            await deleteTestCountryFormats(page);

            const marker = uniqueMarker('FORM');
            const grid = new CountryFormatGridPage(page);
            const form = new CountryFormatFormPage(page);

            try {
                await createCountryFormat(page, 'BE', 'oneline', marker + ' {{var city}}', marker);

                const row = grid.getRow(marker);
                await expect(row).toContainText('Belgium (BE)');
                await expect(row).toContainText('Text One Line');

                await grid.edit(marker);
                await expect(page).toHaveTitle(/Edit Country Format/);
                await expect(form.getCountryField()).toHaveValue('BE');
                await expect(form.getTypeField()).toHaveValue('oneline');
                await expect(form.getFormatField()).toHaveValue(marker + ' {{var city}}');
                await expect(getButton(page, 'Delete')).toBeVisible();

                const updatedFormat = marker + ' UPDATED {{var postcode}}';
                await form.fillFormat(updatedFormat);
                await form.saveAndContinue();

                await page.reload();
                await form.expectToBeLoaded();
                await expect(form.getFormatField()).toHaveValue(updatedFormat);

                await form.delete();
                await expect(grid.getRow(marker)).toHaveCount(0);
            } finally {
                await deleteTestCountryFormats(page);
            }
        });

        test(`rejects a second country format for the same country and type (${theme.name})`, async ({page}) => {
            await configureWithTheme(page, withTheme(config, theme));
            await new AdminLogin(page).login();
            await deleteTestCountryFormats(page);

            const marker = uniqueMarker('DUPLICATE');
            const grid = new CountryFormatGridPage(page);
            const form = new CountryFormatFormPage(page);

            try {
                await createCountryFormat(page, 'LU', 'pdf', marker + ' ORIGINAL', marker);

                await grid.open();
                await grid.addNew();
                await form.fill('LU', 'pdf', marker + ' DUPLICATE');
                await form.saveAndContinue();

                await expectMessage(page, 'error', 'A country format of type "PDF" already exists for the country "Luxembourg".');

                await grid.open();
                await expect(grid.getRow(marker)).toHaveCount(1);
                await expect(grid.getRow(marker)).toContainText(marker + ' ORIGINAL');
            } finally {
                await deleteTestCountryFormats(page);
            }
        });

        test(`deletes a country format via the "Delete" button (${theme.name})`, async ({page}) => {
            await configureWithTheme(page, withTheme(config, theme));
            await new AdminLogin(page).login();
            await deleteTestCountryFormats(page);

            const marker = uniqueMarker('DELETE');
            const grid = new CountryFormatGridPage(page);

            try {
                await createCountryFormat(page, 'BE', 'pdf', marker, marker);
                await grid.edit(marker);
                await new CountryFormatFormPage(page).clickDelete();
                await expect(grid.getRow(marker)).toHaveCount(0);
            } finally {
                await deleteTestCountryFormats(page);
            }
        });
    }
});
