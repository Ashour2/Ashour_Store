<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUSES = ['new', 'confirmed', 'delivered', 'cancelled'];

    function user() {
        return $this->belongsTo(User::class)->withDefault();
    }

    function order_details() {
        return $this->HasMany(OrderDetail::class);
    }

    function payment() {
        return $this->hasOne(Payment::class);
    }

    /** Bootstrap contextual colour for the current status. */
    function getStatusColorAttribute() {
        return [
            'new'       => 'warning',
            'confirmed' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ][$this->status] ?? 'secondary';
    }

    /** Digits-only customer phone, ready for a wa.me link. */
    function getWaPhoneAttribute() {
        return preg_replace('/[^0-9]/', '', $this->customer_phone);
    }

    /** Total units ordered across all lines. */
    function getItemsCountAttribute() {
        return (int) $this->order_details->sum('quantity');
    }

    /**
     * Human readable order summary used for the WhatsApp message, both from
     * the public checkout and from the dashboard.
     */
    function whatsappText(): string
    {
        $lines = [
            __('site.wa_order_title', ['number' => $this->order_number]),
            '',
        ];

        foreach ($this->order_details as $detail) {
            $lines[] = '• ' . $detail->trans_product_name . ' × ' . $detail->quantity
                . ($detail->total > 0 ? ' = ' . number_format($detail->total, 2) : '');
        }

        $lines[] = '';
        if ($this->total > 0) {
            $lines[] = __('site.total') . ': ' . number_format($this->total, 2);
        }
        $lines[] = __('site.your_name') . ': ' . $this->customer_name;
        $lines[] = __('site.your_phone') . ': ' . $this->customer_phone;

        if ($this->customer_address) {
            $lines[] = __('site.address') . ': ' . $this->customer_address;
        }
        if ($this->note) {
            $lines[] = __('site.order_note') . ': ' . $this->note;
        }

        return implode("\n", $lines);
    }

    /** wa.me link that opens the order summary in WhatsApp. */
    function whatsappLink(?string $phone = null): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone ?: setting('whatsapp_number'));

        return 'https://wa.me/' . $phone . '?text=' . urlencode($this->whatsappText());
    }
}
