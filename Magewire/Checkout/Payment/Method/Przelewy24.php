<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Przelewy24 payment method on Hyvä Checkout.
 */
class Przelewy24 extends Base
{
    protected string $methodCode = 'unzer_przelewy24';
}
