<?php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    public function pay(float $amount, array $details = []):mixed;
}
