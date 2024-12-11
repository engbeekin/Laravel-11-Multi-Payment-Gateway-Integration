<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if(session('message'))
                    <h2 class="alert alert-success text-white bg-success">{{session('message')}}</h2>
                @endif
                @if(session('error'))
                    <h2 class="alert alert-danger text-white">  {{session('error')}}</h2>
                @endif
                <div class="p-6 text-gray-900">
                    <div class="card"
                         style="width: 18rem; border-radius: 10px; overflow: hidden; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                        <img
                            src="https://www.apple.com/newsroom/images/2024/09/apple-debuts-iphone-16-pro-and-iphone-16-pro-max/article/Apple-iPhone-16-Pro-hero-geo-240909_inline.jpg.large.jpg"
                            class="border rounded-lg p-2" alt="iPhone 16">
                        <div class="card-body text-center">
                            <h5 class="card-title">iPhone 16</h5>
                            <p class="card-text text-muted">$1,299.00</p>
                            <form action="{{ route('pay') }}" method="post">
                                @csrf
                                <input type="hidden" name="price" value="10">
                                <input type="hidden" name="product_name" value="iphone 16">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" id="payment_method" name="payment_method" value="stripe">
                                <div class="d-flex justify-content-between mt-2">
                                    <button class="btn btn-warning btn-sm me-0 mr-0"
                                            onclick="setPaymentMethod('stripe')">Pay By Card
                                    </button>
                                    <button class="btn btn-primary btn-sm ml-0"
                                            onclick="setPaymentMethod('paypal')">Pay By PayPal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function setPaymentMethod(method) {
            document.getElementById('payment_method').value = method;
        }
    </script>
</x-app-layout>

