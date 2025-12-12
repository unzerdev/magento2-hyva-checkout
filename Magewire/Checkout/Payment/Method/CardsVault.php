<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Unzer CardsVault payment method on Hyvä Checkout.
 */
class CardsVault extends Vault
{
    /** @var string Magento payment method code */
    protected string $methodCode = 'unzer_cards_vault';
}
