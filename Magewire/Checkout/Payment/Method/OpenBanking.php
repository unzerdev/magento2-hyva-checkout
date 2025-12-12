<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for OpenBanking payment method on Hyvä Checkout.
 */
class OpenBanking extends Base
{
    protected string $methodCode = 'unzer_open_banking';
}
