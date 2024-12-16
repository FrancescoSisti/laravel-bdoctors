<?php

namespace App\Http\Controllers\Api;

use Braintree\Gateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BraintreeApiController extends Controller
{
    private $gateway;

    public function __construct()
    {
        $this->gateway = new Gateway([
            'environment' => config('braintree.environment'),
            'merchantId' => config('braintree.merchantId'),
            'publicKey' => config('braintree.publicKey'),
            'privateKey' => config('braintree.privateKey'),
        ]);
    }

    public function generateToken()
    {
        $clientToken = $this->gateway->clientToken()->generate();
        return response()->json(['token' => $clientToken]);
    }

    public function processPayment(Request $request)
    {
        try {
            $result = $this->gateway->transaction()->sale([
                'amount' => $request->amount,
                'paymentMethodNonce' => $request->payment_method_nonce,
                'options' => [
                    'submitForSettlement' => true
                ]
            ]);

            if ($result->success) {
                return response()->json([
                    'success' => true,
                    'transaction' => [
                        'id' => $result->transaction->id,
                        'amount' => $result->transaction->amount,
                        'status' => $result->transaction->status
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction failed',
                    'errors' => $result->errors->deepAll()
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}