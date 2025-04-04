<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $table = 'approval_requests_view';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOriginalModel()
    {
        if (property_exists($this, 'next_created_date') || isset($this->attributes['next_created_date'])) {
            return RecurringOrder::find($this->id);
        } else {
            return Order::find($this->id);
        }
    }
}
