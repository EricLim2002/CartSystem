<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class MergeSessionCartIntoUser
{
    public function handle(Login $event)
    {
        $user = $event->user;

        // Get session cart (guest)
        $sessionCart = session('cart', ['items' => [], 'count' => 0, 'total' => 0]);

        // User cart from DB
        $userCart = $user->cart ?? ['items' => [], 'count' => 0, 'total' => 0];

        // Basic merge: quantities add up.
        // You can copy the method logic from CartController::mergeCarts,
        // but here we do a simple merge inline:
        foreach ($sessionCart['items'] as $id => $line) {
            if (! isset($userCart['items'][$id])) {
                $userCart['items'][$id] = $line;
            } else {
                $userCart['items'][$id]['quantity'] = ($userCart['items'][$id]['quantity'] ?? 0) + ($line['quantity'] ?? 0);
            }
            $userCart['items'][$id]['unit_price'] = (float)($userCart['items'][$id]['unit_price'] ?? $line['unit_price'] ?? 0);
            $userCart['items'][$id]['subtotal'] = round($userCart['items'][$id]['unit_price'] * $userCart['items'][$id]['quantity'], 2);
        }

        // Recompute totals
        $count = 0; $total = 0.0;
        foreach ($userCart['items'] as $line) {
            $count += (int)($line['quantity'] ?? 0);
            $total += (float)($line['subtotal'] ?? 0);
        }
        $userCart['count'] = $count;
        $userCart['total'] = round($total, 2);

        // Persist to DB and to session so both are in sync
        $user->cart = $userCart;
        $user->save();

        session(['cart' => $userCart]);
    }
}
