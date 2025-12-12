<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Unzer Klarna payment method on Hyvä Checkout.
 */
class Klarna extends Base
{
    protected string $methodCode = 'unzer_klarna';
}
