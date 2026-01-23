<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;

class PaymentController extends Controller {


    public function initPayment(Request $request) {
        $user = Auth::user();
        $tran_id = "TXN_" . uniqid();

        $order = new Order();
        $order->user_id = $user->id;
        $order->total_amount = $request->total_amount;
        $order->transaction_id = $tran_id;
        $order->status = 'Pending';
        $order->save();

        $post_data = [
            'store_id' => 'futur69719d7111f42',
            'store_passwd' => 'futur69719d7111f42@ssl',
            'total_amount' => $request->total_amount,
            'currency' => "BDT",
            'tran_id' => $tran_id,
            'success_url' => url('/api/payment/success'),
            'fail_url' => url('/api/payment/fail'),
            'cancel_url' => url('/api/payment/cancel'),
            'cus_name' => $user->name ?? 'Customer Name',
            'cus_email' => $user->email ?? 'customer@mail.com',
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1212',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01700000000',
            'shipping_method' => 'NO',
            'product_name' => 'Ecommerce Items',
            'product_category' => 'General',
            'product_profile' => 'general',
        ];

        $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($handle);
        $result = json_decode($response, true);
        curl_close($handle);

        if ($result && isset($result['status']) && $result['status'] == 'SUCCESS') {
            return response()->json(['url' => $result['GatewayPageURL']]);
        }
        return response()->json(['error' => 'Payment Initiation Failed'], 400);
    }


    public function success(Request $request) {
        $tran_id = $request->input('tran_id');

     
        $order = Order::where('transaction_id', $tran_id)->first();

        if ($order) {
     
            $order->status = 'Confirmed';
            $order->payment_status = 'Paid'; 
            $order->save();

          
            // Auth::id
            Cart::where('user_id', $order->user_id)->delete();

         
            return response()->json([
                'status' => 'success',
                'transaction_id' => $tran_id,
                'message' => 'Payment Successful and Cart Cleared'
            ], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
    }

    public function fail(Request $request) {
        $tran_id = $request->input('tran_id');
        $order = Order::where('transaction_id', $tran_id)->first();
        if ($order) {
            $order->status = 'Failed';
            $order->save();
        }
        return response()->json(['status' => 'fail', 'message' => 'Payment Failed']);
    }
}
