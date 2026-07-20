<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'shipment_number',
    'origin_port_id',
    'destination_port_id',
    'status',
    'transport_mode',
    'company_warehouse_lat',
    'company_warehouse_lng'
])]
class ImportShipment extends Model
{
    /**
     * Get the user associated with this shipment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the origin port of the shipment.
     */
    public function originPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'origin_port_id');
    }

    /**
     * Get the destination port of the shipment.
     */
    public function destinationPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'destination_port_id');
    }
}
