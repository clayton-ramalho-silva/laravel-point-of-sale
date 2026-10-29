<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WoocommerceWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Handle the incoming webhook request from WooCommerce
        $payload = $request->all();

        // You can log the payload for debugging purposes
        \Log::info('WooCommerce Webhook Payload:', $payload);

        // Process the payload as needed
        // For example, you might want to update your database or trigger other actions

        return response()->json(['status' => 'success'], 200);
    }
}
