(function() {
    'use strict';

    const { registerBlockType } = wp.blocks;
    const { createElement } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('car-sell-shop/custom-action-hook', {
        title: __('Custom Action Hook', 'car-sell-shop'),
        description: __('Displays content from the custom action hook', 'car-sell-shop'),
        category: 'widgets',
        icon: 'admin-generic',
        supports: {
            html: false,
            align: ['wide', 'full']
        },
        attributes: {
            content: {
                type: 'string',
                default: ''
            }
        },
        edit: function(props) {
            return createElement('div', {
                className: 'custom-action-hook-container'
            }, [
                createElement('h3', {}, __('Custom Action Hook Content', 'car-sell-shop')),
                createElement('p', {}, __('This block will display content from the car_sell_shop_after_custom_template_content action hook.', 'car-sell-shop')),
                createElement('p', {}, __('Content will be rendered on the frontend.', 'car-sell-shop'))
            ]);
        },
        save: function() {
            return null; // Use render callback
        }
    });
})();
