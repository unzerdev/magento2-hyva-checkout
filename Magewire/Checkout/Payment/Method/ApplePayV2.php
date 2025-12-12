<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for ApplePayV2 payment method on Hyvä Checkout.
 */
class ApplePayV2 extends Base
{
    protected string $methodCode = 'unzer_applepayv2';
}
