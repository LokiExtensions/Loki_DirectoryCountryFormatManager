import {expect, type Locator, type Page} from '@playwright/test';

export type Address = {
    firstname: string;
    lastname: string;
    telephone: string;
    street: string;
    city: string;
    postcode: string;
    countryId: string;
};

export const NL_ADDRESS: Address = {
    firstname: 'Loki',
    lastname: 'Tester',
    telephone: '0201234567',
    street: 'Damrak 1',
    city: 'Amsterdam',
    postcode: '1012 LG',
    countryId: 'NL',
};

/**
 * Customer address book, using selectors that are shared between Luma and Hyvä
 */
export class AddressBookPage {
    constructor(private readonly page: Page) {
    }

    async open() {
        await this.page.goto('/customer/address/');
        await expect(this.page).toHaveURL(/customer\/address/);
    }

    getContent(): Locator {
        return this.page.locator('#maincontent');
    }

    /**
     * Add a new address and make it the default billing address, so that it is rendered via the "html" address template
     */
    async addDefaultBillingAddress(address: Address) {
        await this.page.goto('/customer/address/new/');

        const form = this.page.locator('form[action*="customer/address/formPost"]');
        await expect(form).toBeVisible();

        await form.locator('input[name="firstname"]').fill(address.firstname);
        await form.locator('input[name="lastname"]').fill(address.lastname);
        await form.locator('input[name="telephone"]').fill(address.telephone);
        await form.locator('select[name="country_id"]').selectOption(address.countryId);
        await form.locator('#street_1').fill(address.street);
        await form.locator('input[name="city"]').fill(address.city);
        await form.locator('input[name="postcode"]').fill(address.postcode);

        const defaultBilling = form.locator('input[type="checkbox"][name="default_billing"]');
        if (await defaultBilling.count() > 0) {
            await defaultBilling.check();
        }

        await form.locator('button[type="submit"]').first().click();
        await expect(this.page).toHaveURL(/customer\/address\/(index\/)?(\?.*)?$/, {timeout: 15_000});
    }
}
