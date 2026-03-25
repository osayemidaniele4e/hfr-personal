<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    public $timestamps = false;

    protected $table = 'api_request_logs';

    protected $fillable = [
        'api_client_id',
        'method',
        'endpoint',
        'ip_address',
        'user_agent',
        'status_code',
        'response_time_ms',
        'query_params',
        'created_at',
    ];

    protected $casts = [
        'query_params' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship: API client.
     */
    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }
}
