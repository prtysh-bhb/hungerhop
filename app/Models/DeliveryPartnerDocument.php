<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryPartnerDocument extends Model
{
    use Auditable, HasFactory, Searchable, SoftDeletes, TracksActivity;

    protected $fillable = [
        'partner_id',
        'document_type',
        'document_path',
        'document_name',
        'file_size',
        'mime_type',
        'status',
        'rejection_reason',
        'uploaded_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected array $searchable = [
        'document_name',
        'document_type',
        'status',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function partner()
    {
        return $this->belongsTo(DeliveryPartner::class, 'partner_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
