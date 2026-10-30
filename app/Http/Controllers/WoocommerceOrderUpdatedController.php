<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WoocommerceOrderUpdatedController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret = config('services.woocommerce.webhook_secret');
        $signature = $request->header('X-WC-Webhook-Signature');
        $payload = $request->getContent();

        // 1. Validação da Assinatura (igual ao outro webhook)
        if (empty($signature) || empty($secret)) {
            return response()->json(['error' => 'Missing signature'], 401);
        }

        $calculated = base64_encode(hash_hmac('sha256', $payload, $secret, true));
        if (!hash_equals($calculated, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $orderData = json_decode($payload, true);
        $wooOrderId = $orderData['id'] ?? null;
        $newStatus = $orderData['status'] ?? null;

        if (!$wooOrderId) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // 2. Só processa se for cancelamento ou reembolso
        if (!in_array($newStatus, ['cancelled', 'refunded'])) {
            return response()->json(['status' => 'ignored', 'message' => 'Status não é cancelamento/reembolso'], 200);
        }

        // 3. Busca o pedido local correspondente
        $localOrder = Order::where('payment_type', 'WooCommerce #' . $wooOrderId)->first();

        if (!$localOrder) {
            Log::warning("Pedido E-commerce #{$wooOrderId} não encontrado no Laravel para cancelamento.");
            return response()->json(['status' => 'ignored', 'message' => 'Pedido não encontrado'], 200);
        }

        // Se o pedido local já não está "complete", ignora para não devolver estoque duas vezes
        if ($localOrder->order_status !== 'complete') {
            Log::info("Pedido #{$wooOrderId} já foi processado/alterado. Ignorando devolução.");
            return response()->json(['status' => 'ignored'], 200);
        }

        // 4. Devolve o estoque e atualiza o status
        try {
            DB::transaction(function () use ($localOrder, $newStatus, $wooOrderId) {
                $details = OrderDetails::where('order_id', $localOrder->id)->get();

                foreach ($details as $detail) {
                    $product = Product::where('id', $detail->product_id)->lockForUpdate()->first();
                    if ($product) {
                        $product->increment('stock', $detail->quantity);
                        Log::info("Estoque do produto ID {$product->id} devolvido: +{$detail->quantity} unidades (Pedido #{$wooOrderId}).");
                    }
                }

                $localOrder->update(['order_status' => $newStatus]);
                Log::info("Pedido #{$wooOrderId} atualizado para status '{$newStatus}' no Laravel.");
            });

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Erro ao processar cancelamento/reembolso: ' . $e->getMessage(), ['order_id' => $wooOrderId]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
