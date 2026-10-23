import {expect, test, type Locator, type Page} from '@playwright/test';

const env = (names: string[], fallback: string): string => {
    for (const name of names) {
        const value = process.env[name]?.trim();
        if (value) {
            return value;
        }
    }

    return fallback;
};

export const adminPath = env(['MAGENTO_BACKEND_FRONTNAME', 'ADMIN_PATH'], 'admin');
export const adminUser = env(['MAGENTO_ADMIN_USER', 'ADMIN_USER'], 'admin@loki-checkout.com');
export const adminPassword = env(['MAGENTO_ADMIN_PASSWORD', 'ADMIN_PASSWORD'], 'P@ssw0rd1234');

export const GRID_PATH = 'directory_country_format_manager/index/grid';
export const FORM_PATH = 'directory_country_format_manager/index/form';

/**
 * Prefix for all formats created by these tests, so that leftovers of earlier runs can be cleaned up
 */
export const MARKER_PREFIX = 'LOKI-PW';

const LOKI_POST_URL = 'loki_components/index/html';

/**
 * Apply configuration (Magento config, modules, theme, customer) via the functional tests endpoint
 */
export async function configure(page: Page, config: any): Promise<any> {
    const projectName = test.info().project.name;
    const modules: string[] = Array.isArray(config.modules) ? [...config.modules] : [];
    if (projectName && !modules.includes(projectName)) {
        modules.unshift(projectName);
    }

    const token = process.env.TEST_TOKEN;
    if (!token) {
        throw new Error('TEST_TOKEN is not set');
    }

    const response = await page.request.post(
        '/loki_functional_tests/index/configure?token=' + encodeURIComponent(token),
        {form: {config: JSON.stringify({...config, modules})}}
    );

    const body = await response.text();
    let data: any;
    try {
        data = JSON.parse(body);
    } catch (error) {
        console.error('Expected JSON but received something else', body);
        throw new Error('Bad JSON formatting');
    }

    if (data.error) {
        throw new Error('Configure error: ' + data.error);
    }

    const moduleResult = data.result?.modules ?? {};
    const moduleErrors = [...(moduleResult.enable_errors ?? []), ...(moduleResult.disable_errors ?? [])];
    if (moduleErrors.length > 0) {
        throw new Error('Module status errors: ' + moduleErrors.join('; '));
    }

    return data.result ?? {};
}

/**
 * Configure the environment and skip the current test if the requested theme is not available
 */
export async function configureWithTheme(page: Page, config: any) {
    const result = await configure(page, config);
    const themeErrors: string[] = result.theme?.errors ?? [];
    test.skip(themeErrors.length > 0, themeErrors.join('; '));

    return result;
}

export const uniqueMarker = (name: string): string =>
    `${MARKER_PREFIX}-${name}-${Date.now()}-${Math.floor(Math.random() * 10000)}`;

export class AdminLogin {
    constructor(private readonly page: Page) {
    }

    async login() {
        await this.page.goto('/' + adminPath);

        const usernameField = this.page.locator('#username');
        if (await usernameField.count() > 0) {
            await usernameField.fill(adminUser);
            await this.page.locator('#login').fill(adminPassword);
            await this.page.locator('.action-login').click();
        }

        await expect(this.page.locator('.page-header .admin-user')).toBeVisible();
        await dismissAdminPopups(this.page);
    }
}

/**
 * Close popups (release notification, admin usage tracking) that block clicks after logging in
 */
export async function dismissAdminPopups(page: Page) {
    await expect(page.locator('.page-wrapper [data-role="spinner"]:visible')).toHaveCount(0, {timeout: 15000});

    for (let attempt = 0; attempt < 5; attempt++) {
        const popup = page.locator('.modal-popup._show');
        if (await popup.count() === 0) {
            return;
        }

        const lastPopup = popup.last();
        const declineButton = lastPopup.locator('.action-secondary', {hasText: /Don't Allow/i});
        if (await declineButton.count() > 0) {
            await declineButton.first().click();
        } else {
            await lastPopup.locator('.action-close').first().click();
        }

        await expect(lastPopup).toBeHidden();
    }
}

/**
 * Wait until the Alpine component containing the element is initialized; interacting with the element earlier is lost,
 * because Alpine then (re)binds the element to the initial component state
 */
export async function waitForAlpineComponent(page: Page, selector: string) {
    await page.waitForFunction((selector: string) => {
        const element = document.querySelector(selector)?.closest('[x-data]') as any;
        return !!element && Array.isArray(element._x_dataStack);
    }, selector);
}

export function waitForLokiPost(page: Page) {
    return page.waitForResponse(response => response.url().includes(LOKI_POST_URL), {timeout: 15_000});
}

/**
 * Find a page action button by its label; the accessible name may start with an icon font glyph (like " Back")
 */
export function getButton(page: Page, label: string): Locator {
    const escapedLabel = label.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    return page.locator('.page-actions-buttons').getByRole('button', {name: new RegExp(`^\\W*${escapedLabel}$`)});
}

export class CountryFormatGridPage {
    constructor(private readonly page: Page) {
    }

    async open() {
        await this.page.goto('/' + adminPath + '/' + GRID_PATH + '/');
        await this.expectToBeLoaded();
    }

    async expectToBeLoaded() {
        await expect(this.page).toHaveTitle(/Directory Country Format Manager/);
        await expect(this.getTable()).toBeVisible();
        await waitForAlpineComponent(this.page, 'table[data-role="grid"]');
        await expect(this.page.locator('.admin__data-grid-loading-mask').first()).toBeHidden();
    }

    getTable(): Locator {
        return this.page.locator('table[data-role="grid"]');
    }

    getHeaders(): Locator {
        return this.getTable().locator('thead th[data-column]');
    }

    getRows(): Locator {
        return this.getTable().locator('tbody tr.data-row');
    }

    getRow(text: string): Locator {
        return this.getRows().filter({hasText: text});
    }

    async addNew() {
        await getButton(this.page, 'Add New').click();
        await new CountryFormatFormPage(this.page).expectToBeLoaded();
    }

    async edit(text: string) {
        await this.getRow(text).getByRole('link', {name: 'Edit'}).click();
        await new CountryFormatFormPage(this.page).expectToBeLoaded();
    }
}

export class CountryFormatFormPage {
    constructor(private readonly page: Page) {
    }

    async open() {
        await this.page.goto('/' + adminPath + '/' + FORM_PATH + '/');
        await this.expectToBeLoaded();
    }

    async expectToBeLoaded() {
        await expect(this.getFormatField()).toBeVisible();
        await waitForAlpineComponent(this.page, 'textarea[data-name="format"]');
    }

    getCountryField(): Locator {
        return this.page.locator('select[data-name="country_id"]');
    }

    getTypeField(): Locator {
        return this.page.locator('select[data-name="type"]');
    }

    getFormatField(): Locator {
        return this.page.locator('textarea[data-name="format"]');
    }

    async fill(countryId: string, type: string, format: string) {
        await this.getCountryField().selectOption(countryId);
        await this.getTypeField().selectOption(type);
        await this.fillFormat(format);
    }

    async fillFormat(format: string) {
        const formatField = this.getFormatField();
        await formatField.fill(format);
        await formatField.dispatchEvent('change');
    }

    async saveAndClose() {
        await getButton(this.page, 'Save & Close').click();
        await expect(this.page, 'Saving the country format failed').toHaveURL(new RegExp(GRID_PATH), {timeout: 15_000});
        await new CountryFormatGridPage(this.page).expectToBeLoaded();
    }

    async saveAndContinue() {
        const response = waitForLokiPost(this.page);
        await getButton(this.page, 'Save & Continue').click();
        await response;
    }

    /**
     * Delete the current item by submitting the delete action and waiting for it to be processed. The "Delete" button
     * itself is not used, because it navigates away before its queued AJAX request has been sent
     */
    async delete() {
        const response = waitForLokiPost(this.page);
        await this.getFormatField().evaluate((element: HTMLElement) => {
            const component = (element.closest('[x-data]') as any)._x_dataStack[0];
            component.submit({actions: ['delete'], item: component.item});
        });
        await response;
        await new CountryFormatGridPage(this.page).open();
    }

    async clickDelete() {
        await getButton(this.page, 'Delete').click();
        await expect(this.page).toHaveURL(new RegExp(GRID_PATH), {timeout: 15_000});
        await new CountryFormatGridPage(this.page).expectToBeLoaded();
    }
}

/**
 * Create a country format through the admin form, ending up on the grid again
 */
export async function createCountryFormat(page: Page, countryId: string, type: string, format: string, marker: string) {
    const grid = new CountryFormatGridPage(page);
    await grid.open();
    await grid.addNew();

    const form = new CountryFormatFormPage(page);
    await form.fill(countryId, type, format);
    await form.saveAndClose();
    await expect(grid.getRow(marker)).toHaveCount(1);
}

/**
 * Remove all country formats created by these tests (recognized by MARKER_PREFIX), including leftovers of earlier runs
 */
export async function deleteTestCountryFormats(page: Page, marker: string = MARKER_PREFIX) {
    const grid = new CountryFormatGridPage(page);

    for (let attempt = 0; attempt < 10; attempt++) {
        await grid.open();
        if (await grid.getRow(marker).count() === 0) {
            return;
        }

        await grid.getRow(marker).first().getByRole('link', {name: 'Edit'}).click();
        const form = new CountryFormatFormPage(page);
        await form.expectToBeLoaded();
        await form.delete();
    }

    await grid.open();
    await expect(grid.getRow(marker), 'Unable to delete test country formats').toHaveCount(0);
}
