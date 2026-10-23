import coreConfig from '@loki/config';
import merge from '@loki/util/merge';

export type Theme = {
    name: string;
    theme: string;
};

export const themes: Theme[] = [
    {name: 'Luma', theme: 'Magento/luma'},
    {name: 'Hyvä', theme: 'Loki/hyva-dev'},
];

export const withTheme = (config: any, theme: Theme) => merge(config, {theme: theme.theme});

const config = merge(coreConfig, {
    modules: [
        'Loki_DirectoryCountryFormatManager',
    ],
    config: {
        'admin/captcha/enable': 0,
        'admin/security/use_form_key': 0,
        'customer/captcha/enable': 0,
    }
});

export default config;
