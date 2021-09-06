define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'cse_gateway',
                component: 'Apexx_Cse/js/view/payment/method-renderer/cse_gateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
