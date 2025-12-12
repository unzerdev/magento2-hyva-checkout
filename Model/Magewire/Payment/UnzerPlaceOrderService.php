<?php

namespace Unzer\HyvaCheckout\Model\Magewire\Payment;

use Magento\Quote\Model\Quote;
use Hyva\Checkout\Model\Magewire\Payment\AbstractPlaceOrderService;

/**
 * Class UnzerPlaceOrderService.
 *
 * @package Unzer\HyvaCheckout\Model\Magewire\Payment
 */
class UnzerPlaceOrderService extends AbstractPlaceOrderService
{
    /**
     * @var string
     */
    public const REDIRECT_PATH = 'unzer/payment/redirect';

    /**
     * @param Quote $quote
     * @param ?int $orderId
     *
     * @return string
     */
    public function getRedirectUrl(Quote $quote, ?int $orderId = null): string
    {
        return self::REDIRECT_PATH;
    }
}
