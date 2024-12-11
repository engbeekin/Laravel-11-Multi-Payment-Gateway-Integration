<?php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    public function pay(float $amount, array $details = []):mixed;
    public function redirectToGateway($response, float $amount, array $details = []);
    public function completeThePayment($request);
}
