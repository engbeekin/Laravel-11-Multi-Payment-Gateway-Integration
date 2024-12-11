<?php

namespace App\Services\Payments;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\payment;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;
    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    /**
     * @param float $amount
     * @param array $details
     * @return mixed
     */
    public function pay(float $amount, array $details = []): mixed
    {
        try {
            $response = $this->stripe->checkout->sessions->create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $details['currency'],
                            'product_data' => [
                                'name' => $details['product_name'] ?? 'NA',
                            ],
                            'unit_amount' => $amount * 100,
                        ],
                        'quantity' => $details['quantity'],
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('success', ['payment_method' => Payment::STRIPE])
                    . '&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('dashboard'),
            ]);

            return $this->redirectToGateway(response: $response, amount: $amount, details: $details);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Error: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * @param $response
     * @param float $amount
     * @param array $details
     * @return Application|RedirectResponse|Redirector
     */
    public function redirectToGateway(
        $response,
        float $amount,
        array $details = []
    ): Application|Redirector|RedirectResponse
    {
        if (!isset($response['id']) && $response['id'] == null) {
            return redirect()->route('dashbaord');
        }
        session()->put('product_name', $details['product_name']);
        session()->put('quantity', $details['quantity']);
        session()->put('price', $amount);

        return redirect($response->url);
    }

    /**
     * @param $request
     */
    public function completeThePayment($request)
    {
        try {
            $response = $this->stripe->checkout->sessions->retrieve($request->session_id);
            (new PaymentService())->store($response, Payment::STRIPE);
        } catch (Exception $e) {
            Log::error('Stripe Error: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }
}
