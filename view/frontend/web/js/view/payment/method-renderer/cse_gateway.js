define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function ($, Component, additionalValidators,fullScreenLoader,urlBuilder,storage,modal,placeOrderAction, redirectOnSuccessAction) {
        'use strict';
 
        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: true,
                template: 'Apexx_Cse/payment/cse-form',
            },
 
            getCode: function() {
                return 'cse_gateway';
            },

            
             getData: function () {
                var Json = {};
                if($('input[data-apexx="card_number"]').val() !='')
                {
                $('input[data-apexx]').each(function( key ) {

                    if($(this).data('apexx') != "encrypted_data" || $(this).data('apexx') != "masked_card_number"){
                        Json[$( this ).data('apexx')]= $( this ).val();
                    }

                });

                var maskedCardNumber = setMaskedCardNumber($('input[data-apexx = "card_number"]').val());
                var cardField = $('input[data-apexx = "masked_card_number"]');

                if(!fieldIsNull(maskedCardNumber) && !fieldIsNull(cardField)){
                    cardField.val(maskedCardNumber);
                }

                // Encrypt data with the APEXX_Public_Key.
                var publicKeyString = getPublicKey();
                var publicKeyString = checkoutConfig.payment.cse_gateway.encryption_key;
            
                if (publicKeyString.length != 0) {
                    var encrypt = new JSEncrypt();
                    encrypt.setPublicKey(publicKeyString);
                    var encryptedData = encrypt.encrypt(JSON.stringify(Json));
                    $('input[data-apexx="encrypted_data"]').val(encryptedData);
                }
                var cardNumber= $('input[data-apexx="card_number"]').val();
                var expMonth=  $('select[data-apexx="exp_month"]').val();
                var expYear= $('select[data-apexx="exp_year"]').val();
                var encryptedData= $('input[data-apexx="encrypted_data"]').val();
                var mastkedCardNumber= $('input[data-apexx="masked_card_number"]').val();
                var data = {
                    'method': this.getCode(),
                    'additional_data': {
                       'enc_val': encryptedData,
                       'maskedCardNumber': mastkedCardNumber
                    }
                };
            }
            else {
               var data = {
                    'method': this.getCode(),
                    'additional_data': {
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber(),
                        'cc_cid': this.creditCardVerificationNumber()
                    }
                };
            }
            data['additional_data'] = _.extend(data['additional_data'], this.additionalData);
                // this.vaultEnabler.visitAdditionalData(data);
                return data;
            },
            isActive: function() {
                return true;
            },
 
            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },
                        placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    fullScreenLoader.startLoader();
                    self.isPlaceOrderActionAllowed(false);

                    self.getPlaceOrderDeferredObject()
                        .fail(
                            function () {
                              //  alert("fbfgbfgb");
                                fullScreenLoader.stopLoader();
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(
                        function (orderId) {
                          // alert("ooooooo");
                            self.afterPlaceOrder();
                            self.getOrderPaymentStatus(orderId).done(function (responseJSON) {
                                 var response = JSON.parse(responseJSON);
                                if (response.three_ds_required) {
                                self.validateThreeDS2OrPlaceOrder(responseJSON, orderId)
                                }
                                else
                                {
                                 if (self.redirectAfterPlaceOrder) {
                                    redirectOnSuccessAction.execute();
                                    }
                                }
                            });
                           
                        }
                    );
                }
                return false;
            },

            getOrderPaymentStatus: function (orderId) {
                var serviceUrl = urlBuilder.createUrl('/apexxcse/orders/:orderId/payment-status', {
                    orderId: orderId
                });

                return storage.get(serviceUrl);
            },

            validateThreeDS2OrPlaceOrder: function (responseJSON, orderId) {
                var self = this;
                var response = JSON.parse(responseJSON);
                if (response.three_ds_required) {
                    var threeDSecureForm = '<form name="redirectForm" id="redirectForm" action="'+response.acsURL+'" method="POST">';
                    threeDSecureForm += '<input type="hidden" name="PaReq" id="PaReq" value="'+response.paReq+'">';
                    threeDSecureForm += '<input type="hidden" name="TermUrl" id="TermUrl" value="'+BASE_URL+'cse/process/validate3d">';
                    threeDSecureForm += '<input type="hidden" name="MD" id="MD" value="'+response.psp_3d_id+'">';
                    threeDSecureForm += '<input type="submit" style ="display:none" name="submit3DsDirect" id="submit3DsDirect" value="">';
                    threeDSecureForm += '</form>';


                    console.log(threeDSecureForm);


                    var threeDForm = threeDSecureForm;


                    $('#threeDS2Wrapper').append(threeDSecureForm);

                    fullScreenLoader.stopLoader();

                    $(document).ready(function(){
                        $("#redirectForm").submit();
                    });

                }
            },

            renderThreeDS2Component: function (response, orderId) {
                var self = this;
            }
        });
    }
);