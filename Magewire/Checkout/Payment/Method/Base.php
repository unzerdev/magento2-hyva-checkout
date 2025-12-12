<?php

declare(strict_types=1);

namespace Unzer\HyvaCheckout\Magewire\Checkout\Payment\Method;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magewirephp\Magewire\Component\Form;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Unzer\PAPI\Model\Config as UnzerConfig;
use Rakit\Validation\Validator;
use Unzer\PAPI\Model\Method\Observer\BaseDataAssignObserver;

/**
 * Magewire component for Unzer payment methods on Hyvä Checkout.
 */
class Base extends Form
{
    /** Protected props consumed by PHTML/JS */
    protected string $methodCode = '';

    /** @var CheckoutSession */
    protected $checkoutSession;

    /** @var CartRepositoryInterface */
    protected $quoteRepository;

    /** @var ResolverInterface */
    protected $localeResolver;

    /** @var PaymentHelper */
    protected $paymentHelper;

    /** @var UnzerConfig */
    protected $unzerConfig;

    /** @var UrlInterface */
    protected $url;

    /** Public props consumed by PHTML/JS */
    public string $publicKey = '';
    public string $locale = '';
    public array $methodConfig = []; // e.g. enable_click_to_pay, vault_code, ...

    /** Basket & customer snapshot for Unzer v2 */
    public string $email = '';
    public float $grandTotal = 0.0;
    public string $currency = '';
    public string $birthDate = '';

    /** Flattened address data for convenience in JS */
    public array $billing = [];
    public array $shipping = [];

    /** All data*/
    public array $snapshot = [];

    /**
     * @var bool $isVaultEnabled
     */
    public bool $isVaultEnabled = false;

    /**
     * @var string $vaultCode
     */
    public string $vaultCode = '';

    /**
     * @param Validator $validator Input validator for Magewire forms.
     * @param CheckoutSession $checkoutSession Provides access to the current checkout session and quote.
     * @param CartRepositoryInterface $quoteRepository Handles saving and retrieving the quote.
     * @param ResolverInterface $localeResolver Resolves the current store locale (used for Unzer locale).
     * @param PaymentHelper $paymentHelper Loads payment method instances and configs.
     * @param UnzerConfig $unzerConfig Provides Unzer module configuration (public key, mode, etc.).
     * @param UrlInterface $url Builds secure frontend URLs for redirects.
     */
    public function __construct(
        Validator $validator,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        ResolverInterface $localeResolver,
        PaymentHelper $paymentHelper,
        UnzerConfig $unzerConfig,
        UrlInterface $url
    ) {
        parent::__construct($validator);

        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->localeResolver = $localeResolver;
        $this->paymentHelper = $paymentHelper;
        $this->unzerConfig = $unzerConfig;
        $this->url = $url;
    }

    /**
     * Lifecycle hook executed once when the Magewire component is first instantiated.
     *
     * Prepares Unzer configuration data (public key, locale, mode, per-method settings)
     * and extracts a snapshot of the current quote for use in the frontend.
     *
     * @return void
     *
     * @throws LocalizedException
     */
    public function mount(): void
    {
        // Base config (what Luma provider would normally place into window.checkoutConfig.payment.unzer.*)
        $this->publicKey = $this->unzerConfig->getPublicKey() ?? '';
        $this->locale = str_replace('_', '-', $this->localeResolver->getLocale());

        $method = $this->paymentHelper->getMethodInstance($this->methodCode);
        if ($method && \method_exists($method, 'isAvailable') && $method->isAvailable()
            && \method_exists($method, 'getFrontendConfig')) {
            $this->methodConfig = (array)$method->getFrontendConfig();
        }

        if (!empty($this->methodConfig['publicKey'])) {
            $this->publicKey = $this->methodConfig['publicKey'];
        }

        $this->refreshSnapshot();
    }

    /**
     * Public: let JS fetch a fresh snapshot when needed
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function refreshSnapshot(): void
    {
        $snap = $this->buildSnapshotFromQuote();

        $this->snapshot = $snap;

        $this->email = (string)($snap['email'] ?? '');
        $this->birthDate = (string)($snap['birthDate'] ?? '');
        $this->grandTotal = (float)($snap['grandTotal'] ?? 0.0);
        $this->currency = (string)($snap['currency'] ?? '');
        $this->billing = (array)($snap['billing'] ?? []);
        $this->shipping = (array)($snap['shipping'] ?? []);
    }

    /**
     * @param array $data
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setAdditionalData(array $data): void
    {
        $quote = $this->checkoutSession->getQuote();
        isset($data['resourceId']) && $quote->getPayment()->setAdditionalInformation(
            BaseDataAssignObserver::KEY_RESOURCE_ID,
            $data['resourceId']
        );

        isset($data['threatMetrixId']) &&  $quote->getPayment()->setAdditionalInformation(
            BaseDataAssignObserver::KEY_THREAT_METRIX_ID,
            $data['threatMetrixId']
        );

        isset($data['customerId']) &&  $quote->getPayment()->setAdditionalInformation(
            BaseDataAssignObserver::KEY_CUSTOMER_ID,
            $data['customerId']
        );

        $this->quoteRepository->save($quote);
    }

    /**
     * Persists the "Save for later use" flag in the current quote payment data.
     *
     * @param bool $enable Whether the customer wants to save the payment method for later use.
     *
     * @return bool The same flag value that was set (true if enabled, false otherwise).
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setSaveForLater(bool $enable): bool
    {
        $quote = $this->checkoutSession->getQuote();
        $payment = $quote->getPayment();

        // The flag Magento Vault expects to detect tokenization
        $payment->setAdditionalInformation('is_active_payment_token_enabler', $enable ? 1 : 0);

        if (!empty($this->methodConfig['vault_code'])) {
            $payment->setAdditionalInformation('vault_code', (string)$this->methodConfig['vault_code']);
        }

        $this->quoteRepository->save($quote);
        return $enable;
    }

    /**
     * Extracts a lightweight snapshot of the current quote.
     *
     * Collects key customer, basket, and address data that can be sent to the frontend.
     * Used to keep Unzer’s web components synchronized with the Magento quote state.
     *
     * @return array Snapshot containing email, totals, currency, billing, and shipping address.
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function buildSnapshotFromQuote(): array
    {
        $quote = $this->checkoutSession->getQuote();

        $billingAddress = $quote->getBillingAddress();
        $shippingAddress = $quote->getShippingAddress();

        $customer = $quote->getCustomer();
        $dob = ($customer && $customer->getDob()) ? substr((string)$customer->getDob(), 0, 10) : '';

        return [
            'email' => $quote->getCustomerEmail() ?: '',
            'grandTotal' => $quote->getBaseGrandTotal(),
            'currency' => $quote->getBaseCurrencyCode(),
            'birthDate' => $dob,
            'company' => $billingAddress ? (string)$billingAddress->getCompany() : '',
            'billing' => $billingAddress ? [
                'firstname' => (string)$billingAddress->getFirstname(),
                'lastname' => (string)$billingAddress->getLastname(),
                'street' => (string)($billingAddress->getStreetLine(1) ?? ''),
                'postcode' => (string)$billingAddress->getPostcode(),
                'city' => (string)$billingAddress->getCity(),
                'country' => (string)$billingAddress->getCountryId(),
            ] : [],
            'shipping' => $shippingAddress ? [
                'firstname' => (string)$shippingAddress->getFirstname(),
                'lastname' => (string)$shippingAddress->getLastname(),
                'street' => (string)($shippingAddress->getStreetLine(1) ?? ''),
                'postcode' => (string)$shippingAddress->getPostcode(),
                'city' => (string)$shippingAddress->getCity(),
                'country' => (string)$shippingAddress->getCountryId(),
            ] : [],
            'customerType' => ($billingAddress && !empty($billingAddress->getCompany())) ? 'B2B' : 'B2C'
        ];
    }
}
