<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Bancontact payment method on Hyvä Checkout.
 */
class Bancontact extends Base
{
    protected string $methodCode = 'unzer_bancontact';
}
