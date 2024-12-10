<?php

namespace App\Services\Payments;

class PaymentGatewayFactory
{
    /**
     * @param string $paymentMethod
     * @return mixed
     */
    public static function getPaymentGateway(string $paymentMethod):mixed
    {
        return match ($paymentMethod) {
            'stripe' => new StripePaymentGateway(),
            'paypal' => new PaypalPaymentGateway(),
            default => throw new \InvalidArgumentException("Unsupported payment gateway: {$paymentMethod}"),
        };
    }
}
