<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WoocommerceWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret = config('services.woocommerce.webhook_secret');
        $signature = $request->header('X-WC-Webhook-Signature');
        $payload = $request->getContent();

        // se não houver assinatura ou segredo, rejeita imediatamente
        if (empty($signature) || empty($secret)) {
            Log::warning('Woocommerce webhook: assinatura ou segredo ausente.');
            return response()->json(['error' => 'Assinatura ou segredo ausente.'], 401);
        }

        $calculated = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        if (!hash_equals($calculated, $signature)) {
            Log::warning('Woocommerce webhook: assinatura inválida.', [
                'received' => $signature,
            ]);
            return response()->json(['error' => 'Assinatura inválida.'], 401);
        }

        // A partir daqui a requisição é legitima
        $orderData = json_decode($payload, true);

        Log::info('Woocommerce webhook recebido com sucesso.', [
            'order_id' => $orderData['id'] ?? null,
            'status' => $orderData['status'] ?? null,
        ]);

        // Aqui entra a lógica de baixa de estoque (próximo passo).

        return response()->json(['status' => 'success'], 200);
    }
}
