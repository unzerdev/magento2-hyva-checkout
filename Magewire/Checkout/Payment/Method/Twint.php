<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Twint payment method on Hyvä Checkout.
 */
class Twint extends Base
{
    protected string $methodCode = 'unzer_twint';
}
