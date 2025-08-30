<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Resources\OrderViewResource;
use App\Mail\CheckoutCompleted;
use App\Mail\NewOrderMail;
use App\Models\CartItem;
use App\Models\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeController extends Controller
{
    public function success(Request $request)
    {
        $user = auth()->user();
        $session_id = $request->get("session_id");
        $orders = Order::where('stripe_session_id', $session_id)->get();
        if ($orders->count() === 0) {
            abort(404);
        }
        foreach ($orders as $order) {
            if ($order->user_id !== $user->id) {
                abort(403);
            }
        }
        return Inertia::render('Stripe/Success', ['orders' => OrderViewResource::collection($orders)->collection->toArray()]);
    }
    public function failure() {}
    function webhook(Request $request)
    {

        Log::info('Stripe Webhook Triggered');
        Log::info("=====================================================");
        Log::info("=====================================================");

        $stripe = new \Stripe\StripeClient(config('app.stripe_secret'));
        $endpoint_secret = config('app.stripe_webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->headers->get("Stripe-Signature");
        $event = null;
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (UnexpectedValueException $e) {
            Log::error($e);
            return response("Invalid Payload", 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error($e);
            return response("Invalid Payload", 400);
        }
        Log::info("=====================================================");
        Log::info("=====================================================");
        // dd($event,$event->type);
        Log::info($event->type);
        Log::info($event);
        switch ($event->type) {
            case "charge.updated":
                $charge = $event->data->object;
                $transactionId = $charge['balance_transaction'] ?? null;
                $paymentIntent = $charge["payment_intent"] ?? null;
                if ($transactionId && $transactionId) {
                    $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId);
                } else {
                    Log::warning("Charge updated event without balance transaction ID: " . $charge['id']);
                    return response()->json(['status' => 'pending'], 200);
                }
                $orders = Order::where('payment_intent', $paymentIntent)->get();
                $totalAmount = $balanceTransaction['amount'];
                $stripeFee = 0;
                foreach ($balanceTransaction['fee_details'] as $fee_detail) {
                    if ($fee_detail['type'] == 'stripe_fee') {
                        $stripeFee = $fee_detail['amount'];
                    }
                }
                $platformFeePercentage = config('app.platform_fee_pct');
                foreach ($orders as $order) {
                    //TO send email to vendor
                    $vendorShare = $order->total_price / $totalAmount;
                    $order->online_payment_commission = $vendorShare * $stripeFee;
                    $order->website_commission = ($order->total_price - $order->online_payment_commission) / 100 * $platformFeePercentage;
                    $order->vendor_subtotal = $order->total_price - $order->online_payment_commission - $order->website_commission;
                    $order->save();
                    Mail::to($order->vendorUser)->send(new NewOrderMail($order));
                }
                Mail::to($orders[0]->user)->send(new CheckoutCompleted($orders));
                break;
            case "checkout.session.completed":
                $session = $event->data->object;
                $pi = $session["payment_intent"];
                $orders = Order::query()->with(['orderItems'])->where(['stripe_session_id' => $session['id']])->get();
                $productsToDeletedFromCart = [];
                foreach ($orders as $order) {
                    $order->payment_intent = $pi;
                    $order->status = OrderStatusEnum::Paid;
                    $order->save();
                    $productsToDeletedFromCart = [
                        ...$productsToDeletedFromCart,
                        ...$order->orderItems->map(fn($item) => $item->product_id)->toArray(),

                    ];
                    foreach ($order->orderItems as $orderItem) {
                        $options = $orderItem->variation_type_option_ids;
                        $product = $orderItem->product;
                        if ($options) {
                            sort($options);
                            $variation = $product->variations()->where('variation_type_option_ids', $options)->first();
                            if ($variation && $variation->quantity != null) {
                                $product->quantity -= $orderItem->quantity;
                                $product->save();
                            }
                        } else if ($product->quantity != null) {
                            $product->quantity -= $orderItem->quantity;
                            $product->save();
                        }
                    }
                }
                CartItem::query()->where('user_id', $order->user_id)->whereIn('product_id', $productsToDeletedFromCart)->where('saved_for_later', false)->delete();
                break;
            default:
                echo 'Recieved unknown event type ' . $event->type;
        }
         Log::info("============DONE SUCCESSFFULLLY=======================");

        return response('', 200);
    }
    public function connect()
    {
        $user = auth()->user();

        if (!$user->stripe_id) {
            $user->createStripeAccount();
        }

        if (!$user->isStripeAccountActive()) {
            return redirect($user->getStripeAccountLink());
        }

        return back()->with('success', 'Your account is already connected');
    }
}
