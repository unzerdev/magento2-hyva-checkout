<?php
declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Unzer\PAPI\Model\Config as UnzerConfig;
use Rakit\Validation\Validator;
use Magento\Checkout\Model\CompositeConfigProvider;

/**
 * Magewire component for Unzer CardsVault payment method on Hyvä Checkout.
 */
class Vault extends Base
{
    /** @var array<string, mixed>[] List of stored payment tokens for this method */
    public array $tokens = [];

    /** @var CompositeConfigProvider Provides merged checkout configuration */
    protected CompositeConfigProvider $cfgProv;

    public function __construct(
        Validator $validator,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        ResolverInterface $localeResolver,
        PaymentHelper $paymentHelper,
        UnzerConfig $unzerConfig,
        UrlInterface $url,
        CompositeConfigProvider $cfgProv
    ) {
        parent::__construct($validator, $checkoutSession, $quoteRepository,
            $localeResolver, $paymentHelper, $unzerConfig, $url);
        $this->cfgProv = $cfgProv;
    }

    /**
     * @inheritDoc
     */
    public function mount(): void
    {
        parent::mount();
        $this->tokens = $this->extractVaultTokens($this->cfgProv);
    }

    /**
     * Persists selected public hash (vault token), customer id and method title into the current quote.
     *
     * Magento's vault payment method expects `public_hash` in additional information.
     *
     * @param string $publicHash
     *
     * @return string
     */
    public function setVaultPaymentData(string $publicHash): string
    {
        $quote = $this->checkoutSession->getQuote();
        $payment = $quote->getPayment();

        $payment->setMethod($this->methodCode);

        // Required by Vault resolver
        $payment->setAdditionalInformation(PaymentTokenInterface::PUBLIC_HASH, $publicHash);

        // If the customer is logged in, persist the same customer_id the token was created with
        $customerId = (int)$quote->getCustomerId();
        if ($customerId > 0) {
            $payment->setAdditionalInformation(PaymentTokenInterface::CUSTOMER_ID, $customerId);
        }

        $title = $this->methodConfig['title'] ?? 'Stored Credit Card / Debit Card';
        $payment->setAdditionalInformation('method_title', $title);

        $this->quoteRepository->save($quote);
        return $publicHash;
    }

    /**
     * Extracts stored vault tokens for the given payment method from Magento's checkout config.
     *
     * @param CompositeConfigProvider $cfgProv The composite config provider.
     *
     * @return array<int, array<string, string>> List of token details (brand, masked number, expiry, etc.).
     */
    protected function extractVaultTokens(): array
    {
        $cfg = $this->cfgProv->getConfig();
        $out = [];

        foreach ((array)($cfg['payment']['vault'] ?? []) as $row) {
            $code = (string)($row['config']['code'] ?? '');
            if ($code !== $this->methodCode) {
                continue;
            }
            $det = (array)($row['config']['details'] ?? []);
            $hash = (string)($row['config']['publicHash'] ?? '');
            if ($hash === '') {
                continue;
            }
            $out[] = $this->mapTokenDetails($det, $hash);
        }

        return $out;
    }

    /**
     * Hook for subclasses to control how details are presented.
     * Default = card-style label: "Brand **** (Expires)".
     *
     * @param array<string,mixed> $det Raw "details" from checkout config.
     * @param string $hash Public hash.
     *
     * @return array<string,string> At least: public_hash, label. You may add extra fields.
     */
    protected function mapTokenDetails(array $det, string $hash): array
    {
        $brand = (string)($det['cardBrand'] ?? $det['type'] ?? 'Card');
        $masked = (string)($det['maskedCC'] ?? '****');
        $expires = (string)($det['formattedExpirationDate'] ?? ($det['expirationDate'] ?? ''));

        return [
            'public_hash' => $hash,
            'brand' => $brand,
            'masked' => $masked,
            'expires' => $expires,
        ];
    }
    /**
     * @return array[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}

