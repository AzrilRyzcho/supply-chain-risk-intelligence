<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'rate_to_usd', 'fetched_at'])]
class Currency extends Model
{
    // No explicit relationships needed for general currency cache
}
