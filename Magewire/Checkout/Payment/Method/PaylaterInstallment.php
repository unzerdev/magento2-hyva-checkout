<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Unzer Cards payment method on Hyvä Checkout.
 */
class PaylaterInstallment extends Base
{
    protected string $methodCode = 'unzer_paylater_installment';
}
