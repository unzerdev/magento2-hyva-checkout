<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Ideal payment method on Hyvä Checkout.
 */
class Ideal extends Base
{
    protected string $methodCode = 'unzer_ideal';
}
