<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Wechatpay payment method on Hyvä Checkout.
 */
class Wechatpay extends Base
{
    protected string $methodCode = 'unzer_wechatpay';
}
