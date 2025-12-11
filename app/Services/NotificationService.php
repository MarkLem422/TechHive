<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use App\Events\NotificationCreated;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function notifyOrderPlaced(Order $order)
    {
        // In-app notification for customer
        if ($order->customer) {
            $notification = AppNotification::create([
                'customer_id' => $order->customer->id,
                'order_id' => $order->id,
                'type' => 'order_placed',
                'title' => 'Order Placed Successfully',
                'message' => "Your order #{$order->id} has been placed successfully. Total: ₱" . number_format($order->total_amount, 2),
            ]);

            // Broadcast real-time notification (if broadcasting is configured)
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                // Broadcasting not configured or failed, continue without it
                // Polling will still work
            }

            // Email notification (placeholder - implement email sending)
            // Mail::to($order->customer->email)->send(new OrderPlacedMail($order));
        }

        // Notify sellers
        $sellers = User::where('role', 'seller')->get();
        foreach ($sellers as $seller) {
            $notification = AppNotification::create([
                'user_id' => $seller->id,
                'order_id' => $order->id,
                'type' => 'new_order',
                'title' => 'New Order Received',
                'message' => "New order #{$order->id} from {$order->shipping_name}. Total: ₱" . number_format($order->total_amount, 2),
            ]);

            // Broadcast real-time notification (if broadcasting is configured)
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                // Broadcasting not configured or failed, continue without it
                // Polling will still work
            }
        }
    }

    public static function notifyOrderShipped(Order $order)
    {
        if ($order->customer) {
            $notification = AppNotification::create([
                'customer_id' => $order->customer->id,
                'order_id' => $order->id,
                'type' => 'order_shipped',
                'title' => 'Order Shipped',
                'message' => "Your order #{$order->id} has been shipped and is on its way!",
            ]);

            // Broadcast real-time notification (if broadcasting is configured)
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                // Broadcasting not configured or failed, continue without it
                // Polling will still work
            }

            // Email notification
            // Mail::to($order->customer->email)->send(new OrderShippedMail($order));
        }
    }

    public static function notifyOrderPaid(Order $order)
    {
        if ($order->customer) {
            $notification = AppNotification::create([
                'customer_id' => $order->customer->id,
                'order_id' => $order->id,
                'type' => 'order_paid',
                'title' => 'Payment Confirmed',
                'message' => "Your order #{$order->id} has been marked as paid.",
            ]);

            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                // fallback to polling
            }
        }
    }

    public static function notifyOrderCancelled(Order $order)
    {
        if ($order->customer) {
            $notification = AppNotification::create([
                'customer_id' => $order->customer->id,
                'order_id' => $order->id,
                'type' => 'order_cancelled',
                'title' => 'Order Cancelled',
                'message' => "Your order #{$order->id} has been cancelled.",
            ]);

            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                // fallback to polling
            }
        }
    }

    public static function notifyOrderDelivered(Order $order)
    {
        if ($order->customer) {
            $notification = AppNotification::create([
                'customer_id' => $order->customer->id,
                'order_id' => $order->id,
                'type' => 'order_delivered',
                'title' => 'Order Delivered',
                'message' => "Your order #{$order->id} has been delivered. Thank you for shopping with us!",
            ]);

            // Broadcast real-time notification (if broadcasting is configured)
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                // Broadcasting not configured or failed, continue without it
                // Polling will still work
            }

            // Email notification
            // Mail::to($order->customer->email)->send(new OrderDeliveredMail($order));
        }
    }

    public static function notifyStockAlert($variation, $threshold = 10)
    {
        $sellers = User::where('role', 'seller')->get();
        foreach ($sellers as $seller) {
            $notification = AppNotification::create([
                'user_id' => $seller->id,
                'type' => 'stock_alert',
                'title' => 'Low Stock Alert',
                'message' => "{$variation->product->product_name} - {$variation->variation_name} is running low. Stock: {$variation->stock_quantity}",
            ]);

            // Broadcast real-time notification (if broadcasting is configured)
            try {
                event(new NotificationCreated($notification));
            } catch (\Exception $e) {
                // Broadcasting not configured or failed, continue without it
                // Polling will still work
            }
        }
    }
}

