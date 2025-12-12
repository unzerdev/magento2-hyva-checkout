# Unzer Payment plugin for Hyvä Checkout Magento 2
Hyvä Checkout Compatibility module for Unzer payment plugin.

## Supported payment methods

Unzer payment plugin includes the following payment methods:
* [Alipay](https://docs.unzer.com/payment-methods/alipay/)
* [Apple Pay](https://docs.unzer.com/payment-methods/applepay/)
* [Bancontact](https://docs.unzer.com/payment-methods/bancontact/)
* [Cards and Click to Pay (CTP)](https://docs.unzer.com/payment-methods/card/)
* [EPS](https://docs.unzer.com/payment-methods/eps/)
* [Google Pay](https://docs.unzer.com/payment-methods/googlepay/#page-title)
* [iDEAL](https://docs.unzer.com/payment-methods/ideal/)
* [Klarna](https://docs.unzer.com/payment-methods/klarna/)
* [PayPal](https://docs.unzer.com/payment-methods/paypal/)
* [Przelewy24](https://docs.unzer.com/payment-methods/przelewy24/)
* [TWINT](https://docs.unzer.com/payment-methods/twint/)
* [Unzer Direct Bank Transfer](https://docs.unzer.com/payment-methods/open-banking/)
* [Unzer Direct Debit](https://docs.unzer.com/payment-methods/unzer-direct-debit/)
* [Direct Debit Secured](https://docs.unzer.com/payment-methods/direct-debit-secured/)
* [Unzer Installment](https://docs.unzer.com/payment-methods/unzer-installment-upl/)
* [Unzer Invoice](https://docs.unzer.com/payment-methods/unzer-invoice-upl/)
* [Unzer Prepayment](https://docs.unzer.com/payment-methods/unzer-prepayment/)
* [WeChat Pay](https://docs.unzer.com/payment-methods/wechat-pay/)
* [Wero](https://docs.unzer.com/payment-methods/wero/)

## Requirements

- Magento 2.4.6, 2.4.7 or 2.4.8
- [Base Unzer Magento 2 Plugin](https://docs.unzer.com/plugins/magento-2/) installed.
- [Hyvä Theme module](https://docs.hyva.io/hyva-themes/getting-started/index.html) installed.
- [Hyvä Checkout module](https://docs.hyva.io/checkout/hyva-checkout/getting-started/index.html) installed and enabled.


## Installation

### Install the Plugin using Composer
```bash
composer require unzerdev/magento2-hyva-checkout
```
### Once Composer has installed the dependencies and the plugin, activate it:

```bash
php bin/magento module:enable Unzer_HyvaCheckout --clear-static-content
```
### Import the plugin schema into your database:

```bash
php bin/magento setup:upgrade
```

### Clear the cache, generate dependency injection, and deploy static files:
```bash
php bin/magento cache:flush
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Support

For any issues or questions please get in touch with our support.

**Email**: support@unzer.com

**Phone**: +49 (0)6221/6471-100

**Twitter**: [@UnzerTech](https://twitter.com/UnzerTech)

**Webpage**: https://unzer.com/
