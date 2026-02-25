<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
        ];
    }
}
