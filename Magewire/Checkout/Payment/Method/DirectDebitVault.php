<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

/**
 * Magewire component for Unzer SEPA Direct Debit vault payment method on Hyvä Checkout.
 */
class DirectDebitVault extends Vault
{
    /** @var string Magento payment method code */
    protected string $methodCode = 'unzer_direct_debit_vault';

    /** @inheritDoc */
    protected function mapTokenDetails(array $det, string $hash): array
    {
        $holder = (string)($det['accountHolder'] ?? '');
        $iban = (string)($det['maskedIban'] ?? '');

        return [
            'public_hash' => $hash,
            'account_holder' => $holder,
            'masked_iban' => $iban,
        ];
    }
}
