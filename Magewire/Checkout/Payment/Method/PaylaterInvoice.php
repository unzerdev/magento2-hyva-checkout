<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Unzer PaylaterInvoice payment method on Hyvä Checkout.
 */
class PaylaterInvoice extends Base
{
    protected string $methodCode = 'unzer_paylater_invoice';
}
