<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class WoocommerceWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret = config('services.woocommerce.webhook_secret');
        $signature = $request->header('X-WC-Webhook-Signature');
        $payload = $request->getContent();

        // 1. Validação da Assinatura
        if (empty($signature) || empty($secret)) {
            Log::warning('Woocommerce webhook: assinatura ou segredo ausente.');
            return response()->json(['error' => 'Assinatura ou segredo ausente.'], 401);
        }

        $calculated = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        if (!hash_equals($calculated, $signature)) {
            Log::warning('Falha na assinatura', ['recebido' => $signature, 'calculado' => $calculated]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // 2. Decodifica o pedido
        $orderData = json_decode($payload, true);
        $wooOrderId = $orderData['id'] ?? null;

        if (!$wooOrderId || empty($orderData['line_items'])) {
            return response()->json(['status' => 'ignored', 'message' => 'Pedido sem itens ou ID'], 200);
        }

        // 🛡️ PROTEÇÃO CONTRA DUPLICIDADE (IDEMPOTÊNCIA)
        $pedidoJaExiste = Order::where('payment_type', 'WooCommerce #' . $wooOrderId)->exists();

        if ($pedidoJaExiste) {
            Log::info("Pedido E-commerce #{$wooOrderId} ignorado: já foi processado anteriormente.");
            return response()->json(['status' => 'ignored', 'message' => 'Pedido já processado'], 200);
        }

        // 3. Processamento da Venda e Baixa de Estoque
        try {
            DB::transaction(function () use ($orderData, $wooOrderId) {

                $total = 0;
                $itemsParaSalvar = [];
                $qtdTotalProdutos = 0;

                // Primeiro loop: Validar estoque, calcular totais e dar baixa
                foreach ($orderData['line_items'] as $item) {
                    $sku = $item['sku'];
                    $quantity = (int) $item['quantity'];
                    $price = (float) $item['price'];

                    // Bloqueia a linha para evitar venda dupla
                    $product = Product::where('code', $sku)->lockForUpdate()->first();

                    if (!$product) {
                        throw new \Exception("Produto com SKU {$sku} não encontrado no estoque físico.");
                    }

                    if ($product->stock < $quantity) {
                        throw new \Exception("Estoque insuficiente para o SKU {$sku}. Disponível: {$product->stock}, solicitado: {$quantity}.");
                    }

                    $totalItem = $quantity * $price;
                    $total += $totalItem;
                    $qtdTotalProdutos += $quantity;

                    $itemsParaSalvar[] = [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $price,
                        'total' => $totalItem,
                    ];

                    // Baixa no estoque
                    $product->decrement('stock', $quantity);
                }

                // Gera a Invoice para o Laravel
                $invoice_no = IdGenerator::generate([
                    'table' => 'orders',
                    'field' => 'invoice_no',
                    'length' => 10,
                    'prefix' => 'INV-'
                ]);

                // Cria o Pedido (Order)
                $order = Order::create([
                    'customer_id' => 5, // ⚠️ ID do cliente "E-commerce" criado por você
                    'invoice_no' => $invoice_no,
                    'order_date' => Carbon::now(),
                    'order_status' => 'complete', // Já entra pago/concluído
                    'total_products' => $qtdTotalProdutos,
                    'sub_total' => $total,
                    'vat' => 0,
                    'total' => $total,
                    'payment_type' => 'WooCommerce #' . $wooOrderId, // 👈 AQUI registramos o ID do WooCommerce
                    'pay_amount' => $total,
                    'due_amount' => 0,
                ]);

                // Salva os Itens do Pedido (OrderDetails)
                foreach ($itemsParaSalvar as $itemData) {
                    $itemData['order_id'] = $order->id;
                    OrderDetails::create($itemData);
                }

                Log::info("Pedido E-commerce #{$wooOrderId} gerado com sucesso no Laravel. Invoice: {$invoice_no}");
            });

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar venda do WooCommerce: ' . $e->getMessage(), [
                'order_id' => $wooOrderId,
            ]);

            // Retorna 200 para evitar loops de reenvio do WooCommerce
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
