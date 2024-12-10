<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentGatewayInterface;
use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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
        } catch (\Exception $e) {
            Log::error('Stripe Error: ' . $e->getMessage());

            return redirect()->route('dasboard')->with('error', $e->getMessage());
        }
    }
}
