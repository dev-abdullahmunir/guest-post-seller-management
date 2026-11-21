<?php

namespace Modules\SellerManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Website extends Model
{
    use SoftDeletes;

    protected $table = 'websites';

    protected $fillable = [
        'vendor_id',
        'website_name',
        'website_url',
        'niche',
        'domain_authority',
        'domain_rating',
        'traffic',
        'vendor_price',
        'turnaround_days',
        'status',
        'notes',
    ];

    protected $casts = [
        'vendor_id' => 'integer',
        'domain_authority' => 'integer',
        'domain_rating' => 'integer',
        'traffic' => 'integer',
        'vendor_price' => 'decimal:2',
        'turnaround_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Vendor/Seller who owns this website inventory.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'vendor_id');
    }
}
