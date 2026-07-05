<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\Request;

class PortApiController extends Controller
{
    public function index(Request $request)
    {
        $ports = Port::with('country')->get();
        return response()->json($ports->map(function ($port) {
            return [
                'id' => $port->id,
                'name' => $port->name,
                'code' => $port->code,
                'country' => $port->country ? $port->country->name : 'N/A',
                'latitude' => $port->latitude,
                'longitude' => $port->longitude,
            ];
        }));
    }
}
