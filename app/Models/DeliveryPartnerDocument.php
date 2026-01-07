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
        'document_path_front',
        'document_path_back',
        'document_format',
        'document_name',
        'document_name_back',
        'file_size',
        'file_size_back',
        'mime_type',
        'mime_type_back',
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
        'file_size_back' => 'integer',
    ];

    /**
     * Get the document paths as an array
     */
    public function getDocumentPaths()
    {
        if ($this->document_format === 'photo_two_side') {
            return [
                'front' => $this->document_path_front,
                'back' => $this->document_path_back,
            ];
        }

        return [
            'front' => $this->document_path ?? $this->document_path_front,
        ];
    }

    /**
     * Check if document has both sides
     */
    public function hasBothSides(): bool
    {
        return $this->document_format === 'photo_two_side' 
            && !empty($this->document_path_front) 
            && !empty($this->document_path_back);
    }

    public function partner()
    {
        return $this->belongsTo(DeliveryPartner::class, 'partner_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
