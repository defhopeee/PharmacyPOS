<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Clean, lightweight pagination markup (no oversized default SVG icons).
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        // Share live notifications (low stock + expiring) with the app shell.
        View::composer('layouts.app', function ($view) {
            $lowstock = Product::whereColumn('quantity', '<=', 'reorder')
                ->orderBy('quantity')
                ->get(['id', 'name', 'quantity', 'reorder']);

            $expiring = Product::whereNotNull('expiry')
                ->whereDate('expiry', '<=', Carbon::today()->addDays(30))
                ->orderBy('expiry')
                ->get(['id', 'name', 'expiry']);

            // Owners manage stock; attendants can only reach the POS.
            $isOwner = optional(auth()->user())->isOwner();
            $stockUrl = $isOwner ? route('owner.products.index') : route('pos.index');

            $notifications = [];

            foreach ($lowstock as $p) {
                $notifications[] = [
                    'type' => $p->quantity == 0 ? 'danger' : 'warning',
                    'icon' => 'package',
                    'title' => $p->quantity == 0 ? "{$p->name} is out of stock" : "{$p->name} is running low",
                    'meta' => "{$p->quantity} left (reorder at {$p->reorder})",
                    'url' => $isOwner ? $stockUrl.'?search='.urlencode($p->name) : $stockUrl,
                ];
            }

            foreach ($expiring as $p) {
                $expired = $p->expiry->isPast();
                $notifications[] = [
                    'type' => $expired ? 'danger' : 'warning',
                    'icon' => 'alert',
                    'title' => $expired ? "{$p->name} has expired" : "{$p->name} expires soon",
                    'meta' => $p->expiry->format('d M Y'),
                    'url' => $isOwner ? $stockUrl.'?search='.urlencode($p->name) : $stockUrl,
                ];
            }

            $view->with('notifications', $notifications)
                ->with('notifcount', count($notifications));
        });
    }
}
