<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Prepayment payment method on Hyvä Checkout.
 */
class Prepayment extends Base
{
    protected string $methodCode = 'unzer_prepayment';
}
