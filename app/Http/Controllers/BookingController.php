<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function create(Destination $destination)
    {
        $paymentMethods = PaymentMethod::active()->ordered()->get();

        return view('bookings.create', compact('destination', 'paymentMethods'));
    }

    public function store(StoreBookingRequest $request, Destination $destination)
    {
        $validated = $request->validated();

        $quantity = $validated['quantity'];
        $unitPrice = $destination->ticket_price;
        $totalAmount = $quantity * $unitPrice;

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'destination_id' => $destination->id,
            'visit_date' => $validated['visit_date'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'visitor_name' => $validated['visitor_name'],
            'visitor_email' => $validated['visitor_email'],
            'visitor_phone' => $validated['visitor_phone'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'awaiting_payment',
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'payment_method_id' => $validated['payment_method_id'],
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking berhasil dibuat! Silakan lakukan pembayaran dalam 24 jam.');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['destination', 'payment.paymentMethod']);

        return view('bookings.show', compact('booking'));
    }

    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['destination', 'payment'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if (! $booking->canCancel()) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }

        $booking->markAsCancelled('Dibatalkan oleh user');

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
