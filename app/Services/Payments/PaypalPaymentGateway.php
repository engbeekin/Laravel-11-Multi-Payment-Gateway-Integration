<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\payment;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalPaymentGateway implements PaymentGatewayInterface
{

    /**
     * @param float $amount
     * @param array $details
     * @return mixed
     */
    public function pay(float $amount, array $details = []): mixed
    {
        try {
            $provider = self::getPaypalProvider();
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('success', ['payment_method' => Payment::PAYPAL]),
                    "cancel_url" => route('dashboard')
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $amount
                        ]
                    ]
                ]
            ]);
            return self::redirectToGateway(response: $response, amount: $amount, details: $details);
        } catch (Exception $e) {
            Log::error('Paypal Error: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * @param $response
     * @param float $amount
     * @param array $details
     * @return Redirector|RedirectResponse
     */
    public function redirectToGateway($response, float $amount, array $details = []): Redirector|RedirectResponse
    {
        if (!isset($response['id']) && $response['id'] == null) {
            return redirect()->route('dashbaord');
        }
        foreach ($response['links'] as $link) {
            if ($link['rel'] == 'approve') {
                session()->put('product_name', $details['product_name']);
                session()->put('quantity', $details['quantity']);
                return redirect()->away($link['href']);
            }
        }
    }

    /**
     * @param $request
     */
    public function completeThePayment($request)
    {
        try {
            $provider = self::getPaypalProvider();
            $paypalToken = $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request->token);
            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                (new PaymentService())->store($response, Payment::PAYPAL);
            }
        } catch (Exception $e) {
            Log::error('Paypal Error: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * @return PayPalClient
     * @throws \Exception
     */
    private function getPaypalProvider(): PayPalClient
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        return $provider;
    }
}

