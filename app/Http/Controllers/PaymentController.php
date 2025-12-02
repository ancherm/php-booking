<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function page(Ticket $order)
    {
        return view('payment.page', compact('order'));
    }

    public function process(Request $request, Ticket $order)
    {
        // 25% шанс отказа
        if (rand(1, 4) === 1) {
            return back()->with('error', 'Оплата не прошла. Попробуйте снова.');
        }

        $order->update(['status' => 'paid']);

        return redirect()->route('payment.success');
    }

    public function success()
    {
        return view('payment.success');
    }
}
