import {test, expect} from '@loki/test';
import merge from '@loki/util/merge';
import config, {themes, withTheme} from './config/config';
import {
    AdminLogin,
    configure,
    configureWithTheme,
    createCountryFormat,
    deleteTestCountryFormats,
    uniqueMarker,
} from './helpers/admin-objects';
import {AddressBookPage, NL_ADDRESS} from './helpers/storefront';

test.describe('Loki_DirectoryCountryFormatManager storefront address rendering', () => {
    for (const theme of themes) {
        test(`renders the default billing address with the managed country format (${theme.name})`, async ({page, browser, baseURL}) => {
            test.setTimeout(240_000);
            const themeConfig = withTheme(config, theme);
            await configureWithTheme(page, themeConfig);

            await new AdminLogin(page).login();
            await deleteTestCountryFormats(page);

            // The storefront gets its own context, because its "form_key" cookie breaks the AJAX calls in the Admin Panel
            const storefrontContext = await browser.newContext({baseURL, ignoreHTTPSErrors: true});
            const marker = uniqueMarker('ADDRESS');

            try {
                await createCountryFormat(
                    page,
                    'NL',
                    'html',
                    `${marker} {{var city}}|{{var postcode}}<br/>{{var firstname}} {{var lastname}}`,
                    marker
                );

                // Use a new customer every time, so that the address becomes the default billing address
                const storefront = await storefrontContext.newPage();
                await configure(storefront, merge(themeConfig, {
                    customer: {
                        login: true,
                        email: `pw-country-format-${Date.now()}@loki-extensions.com`,
                    },
                }));

                const addressBook = new AddressBookPage(storefront);
                await addressBook.addDefaultBillingAddress(NL_ADDRESS);
                await addressBook.open();

                await expect(addressBook.getContent())
                    .toContainText(`${marker} ${NL_ADDRESS.city}|${NL_ADDRESS.postcode}`);
                await expect(addressBook.getContent())
                    .toContainText(`${NL_ADDRESS.firstname} ${NL_ADDRESS.lastname}`);
            } finally {
                await storefrontContext.close();
                await deleteTestCountryFormats(page);
            }
        });
    }
});
