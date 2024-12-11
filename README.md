# Laravel Multi-Payment Gateway Integration

## Overview
This repository demonstrates how to implement multiple payment gateways (e.g., Stripe, PayPal)
in a Laravel application using the **Strategy Design Pattern**. It provides a scalable and maintainable architecture for handling various payment methods.

---

## Features
- Modular design with separate classes for each payment gateway.
- Easily switch between payment gateways using configuration or user preference.
- Fully customizable and extensible to add more payment gateways.
- Built-in input validation and error handling.
- storing the payment details into Db
- reusable code

## Technologies Used
- **Laravel** (v10+)
- **PHP** (v8.1+)

## Installation

## Setup Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/laravel-multi-payment-gateway.git
cd laravel-multi-payment-gateway
```
### 2. Install dependencies
```bash
composer install
```

### 3. Copy the environment file
```bash
cp .env.example .env
```

### 4. Configure your payment gateway credentials in `.env`
```bash
STRIPE_PUBLIC_KEY=
STRIPE_SECRET=

#PayPal API Mode
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=K
PAYPAL_SANDBOX_CLIENT_SECRET=
```

## Configuration

### Adding New Payment Gateways

1. Implement the `PaymentGatewayInterface`
2. Add your gateway to the `PaymentGatewayFactory`

```php
class MyNewPaymentGateway implements PaymentGatewayInterface {
    public function pay(float $amount, array $details): array {
        // Implementation details
    }
    public function pay(float $amount, array $details = []):mixed
    {
        // Implementation details
    };
    public function redirectToGateway($response, float $amount, array $details = [])
    {
       // Implementation details
    };
    public function completeThePayment($request)
    {
       // Implementation details
    };
}

// In PaymentGatewayFactory
public static function getPaymentGateway(string $paymentMethod)
{

    return match($gatewayName) {
        'stripe' => new StripePaymentGateway(),
        'paypal' => new PayPalPaymentGateway(),
        'myNewGateway' => new MyNewPaymentGateway(),
        default => throw new \InvalidArgumentException("Unsupported payment gateway: {$paymentMethod}"),
    };
}
```

## Usage Example

### In a Controller

```php
public function proceedToPay(Request $request)
{
    $paymentMethod = PaymentGatewayFactory::getPaymentGateway($request->payment_method);
    $amount = $request->price;
    $details = [
        'currency' => 'usd',
        'product_name' => $request->product_name,
        'quantity' => $request->quantity ?? 1,
    ];
    
    return $paymentMethod->pay($amount, $details);
}
```

## Supported Gateways

- [x] Stripe
- [x] PayPal
