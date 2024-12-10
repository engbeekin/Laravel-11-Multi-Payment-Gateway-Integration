<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class StripePaymentGateway implements PaymentGatewayInterface
{

    /**
     * @param float $amount
     * @param array $details
     * @return mixed
     */
    public function pay(float $amount, array $details = []): mixed
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $response = $stripe->checkout->sessions->create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $details['currency'],
                            'product_data' => [
                                'name' => $details['product_name'] ?? 'hi',
                            ],
                            'unit_amount' => $amount * 100,
                        ],
                        'quantity' => $details['quantity'],
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('dashboard'),
            ]);

            return self::stripeResponse(response: $response, amount: $amount);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Error: ' . $e->getMessage());

            return redirect()->route('dasboard')->with('error', $e->getMessage());
        }
    }

    /**
     * @param $response
     * @param float $amount
     * @return Application|RedirectResponse|Redirector
     */
    private function stripeResponse($response, float $amount): Application|Redirector|RedirectResponse
    {
        if (isset($response->id) && $response->id != '') {
            session()->put('product_name', 'hi');
            session()->put('quantity', 1);
            session()->put('price', $amount);

            return redirect($response->url);
        } else {
            return redirect()->route('cancel');
        }
    }
}
