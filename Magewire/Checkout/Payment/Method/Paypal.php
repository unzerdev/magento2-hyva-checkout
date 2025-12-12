<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Locale\ResolverInterface;
use Rakit\Validation\Validator;
use Unzer\PAPI\Model\Config as UnzerConfig;

/**
 * Magewire component for Unzer Paypal payment method on Hyvä Checkout.
 */
class Paypal extends Base
{
    protected string $methodCode = 'unzer_paypal';

    /** @var CompositeConfigProvider Provides merged checkout configuration */
    private CompositeConfigProvider $cfgProv;

    /**
     * @inheritDoc
     */
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
        parent::__construct(
            $validator, $checkoutSession, $quoteRepository,
            $localeResolver, $paymentHelper, $unzerConfig, $url
        );
        $this->cfgProv = $cfgProv;
    }

    /**
     * @inheritDoc
     */
    public function mount(): void
    {
        parent::mount();

        $this->vaultCode = (string)($this->methodConfig['vault_code'] ?? 'unzer_paypal_vault');

        $cfg = $this->cfgProv->getConfig();
        $this->isVaultEnabled = !empty($cfg['vault'][$this->vaultCode]['is_enabled']);
    }
}
