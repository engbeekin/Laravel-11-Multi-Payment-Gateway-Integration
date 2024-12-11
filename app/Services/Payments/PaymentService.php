<?php

namespace App\Services\Payments;

use App\Models\Payment;

class PaymentService
{
    /**
     * @param $response
     * @param string $paymentMethod
     * @return void
     */
    public function store($response, string $paymentMethod): void
    {
        $isStripPaymentMethod = $paymentMethod === Payment::STRIPE;
        payment::create([
            'payment_id' => $isStripPaymentMethod ? $response->payment_intent : $response['id'],
            'product_name' => session()->get('product_name'),
            'quantity' => session()->get('quantity'),
            'amount' => $isStripPaymentMethod ? session()->get('price')
                : $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
            'currency' => $isStripPaymentMethod ? $response->currency
                : $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'],
            'customer_name' => $isStripPaymentMethod ? $response->customer_details->name
                : $response['payer']['name']['given_name'],
            'customer_email' => $isStripPaymentMethod ? $response->customer_details->email
                : $response['payer']['email_address'],
            'payment_status' => $isStripPaymentMethod ? $response->status : $response['status'],
            'payment_method' => strtoupper($paymentMethod)
        ]);
    }
}
