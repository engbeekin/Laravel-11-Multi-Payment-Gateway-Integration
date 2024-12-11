<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentGatewayFactory;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function proceedToPay(Request $request): mixed
    {
        try {
            $paymentMethod = PaymentGatewayFactory::getPaymentGateway($request->payment_method);
            $amount = $request->price;
            $details = [
                'currency' => 'usd',
                'product_name' => $request->product_name,
                'quantity' => $request->quantity ?? 1,
            ];

            return $paymentMethod->pay($amount, $details);
        } catch (Exception $e) {
            Log::error('Stripe Error: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function handlePaymentSuccess(Request $request): mixed
    {
        try {
            $paymentMethod = PaymentGatewayFactory::getPaymentGateway($request->payment_method);
            $paymentMethod->completeThePayment($request);
            return view('payment.success');
        } catch (Exception $e) {
            Log::error('Stripe Error: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }
}
