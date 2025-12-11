<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = collect();

        if (Auth::check()) {
            if (Auth::user()->role === 'seller') {
                $notifications = AppNotification::where('user_id', Auth::id())
                    ->latest()
                    ->paginate(20);
            } else {
                $customer = \App\Models\Customer::where('email', Auth::user()->email)->first();
                if ($customer) {
                    $notifications = AppNotification::where('customer_id', $customer->id)
                        ->latest()
                        ->paginate(20);
                }
            }
        }

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(AppNotification $notification)
    {
        if (Auth::check()) {
            $customer = \App\Models\Customer::where('email', Auth::user()->email)->first();
            
            if (Auth::user()->role === 'seller') {
                if ($notification->user_id === Auth::id()) {
                    $notification->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                }
            } else if ($customer && $notification->customer_id === $customer->id) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            }
        }

        return back();
    }

    public function open(AppNotification $notification)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $isOwner = false;
        if (Auth::user()->role === 'seller') {
            $isOwner = $notification->user_id === Auth::id();
        } else {
            $customer = \App\Models\Customer::where('email', Auth::user()->email)->first();
            $isOwner = $customer && $notification->customer_id === $customer->id;
        }

        if ($isOwner) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if ($notification->order_id) {
            if (Auth::user()->role === 'seller') {
                return redirect()->route('seller.orders.show', $notification->order_id);
            }
            return redirect()->route('orders.show', $notification->order_id);
        }

        return redirect()->route('notifications.index');
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'seller') {
                AppNotification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
            } else {
                $customer = \App\Models\Customer::where('email', Auth::user()->email)->first();
                if ($customer) {
                    AppNotification::where('customer_id', $customer->id)
                        ->where('is_read', false)
                        ->update([
                            'is_read' => true,
                            'read_at' => now(),
                        ]);
                }
            }
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function getUnreadCount(Request $request)
    {
        // Only allow AJAX requests to prevent direct access showing JSON
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('dashboard');
        }

        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = 0;
        if (Auth::user()->role === 'seller') {
            $count = AppNotification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
        } else {
            $customer = \App\Models\Customer::where('email', Auth::user()->email)->first();
            if ($customer) {
                $count = AppNotification::where('customer_id', $customer->id)
                    ->where('is_read', false)
                    ->count();
            }
        }

        return response()->json(['count' => $count]);
    }

    public function getLatest(Request $request)
    {
        // Only allow AJAX requests to prevent direct access showing JSON
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('dashboard');
        }

        if (!Auth::check()) {
            return response()->json(['notifications' => []]);
        }

        $notifications = collect();
        if (Auth::user()->role === 'seller') {
            $notifications = AppNotification::where('user_id', Auth::id())
                ->latest()
                ->limit(5)
                ->get();
        } else {
            $customer = \App\Models\Customer::where('email', Auth::user()->email)->first();
            if ($customer) {
                $notifications = AppNotification::where('customer_id', $customer->id)
                    ->latest()
                    ->limit(5)
                    ->get();
            }
        }

        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'order_id' => $notification->order_id,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            })
        ]);
    }
}
