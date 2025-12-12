<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Unzer PaypalVault payment method on Hyvä Checkout.
 */
class PaypalVault extends Vault
{
    /** @var string Magento payment method code */
    protected string $methodCode = 'unzer_paypal_vault';

    /**
     * @inheritDoc
     */
    protected function mapTokenDetails(array $det, string $hash): array
    {
        $email = (string)($det['payerEmail'] ?? $det['email'] ?? '');

        return [
            'public_hash' => $hash,
            'email' => $email,
        ];
    }
}
