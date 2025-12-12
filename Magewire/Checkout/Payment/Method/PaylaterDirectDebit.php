<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Paylater Direct Debit payment method on Hyvä Checkout.
 */
class PaylaterDirectDebit extends Base
{
    protected string $methodCode = 'unzer_paylater_direct_debit';
}
