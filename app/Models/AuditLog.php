<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'record_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $module, ?string $recordId = null, $details = null, ?User $user = null): self
    {
        $user = $user ?? auth()->user();
        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System / Kiosk',
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'details' => is_array($details) ? json_encode($details) : $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
