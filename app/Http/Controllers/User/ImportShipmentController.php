<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ImportShipment;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class ImportShipmentController extends Controller
{
    /**
     * Display a listing of the shipments.
     */
    public function index()
    {
        $shipments = ImportShipment::with(['originPort.country', 'destinationPort.country'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $ports = \App\Models\Port::with('country')->orderBy('name', 'asc')->get();

        return view('user.shipments.index', compact('shipments', 'ports'));
    }

    /**
     * Show the form for creating a new shipment.
     */
    public function create()
    {
        $ports = Port::with('country')->orderBy('name', 'asc')->get();
        // Group ports by country name for optgroups
        $groupedPorts = $ports->groupBy(function ($port) {
            return $port->country->name;
        });

        return view('user.shipments.create', compact('groupedPorts'));
    }

    /**
     * Store a newly created shipment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipment_number' => 'required|string|unique:import_shipments,shipment_number|max:100',
            'origin_port_id' => 'required|exists:ports,id|different:destination_port_id',
            'destination_port_id' => 'required|exists:ports,id',
            'status' => 'required|string|in:Pending,In Transit,Completed,Delayed',
        ], [
            'origin_port_id.different' => 'Pelabuhan asal harus berbeda dengan pelabuhan tujuan.',
        ]);

        $shipment = ImportShipment::create([
            'user_id' => auth()->id(),
            'shipment_number' => $request->shipment_number,
            'origin_port_id' => $request->origin_port_id,
            'destination_port_id' => $request->destination_port_id,
            'transport_mode' => 'Sea Freight',
            'status' => $request->status,
        ]);

        return redirect()->route('user.shipments.route', $shipment)
            ->with('success', 'Rencana pengiriman barang berhasil dibuat.');
    }

    /**
     * Display the specified shipment.
     */
    public function show(ImportShipment $shipment)
    {
        // Ensure user owns the shipment
        if ($shipment->user_id !== auth()->id()) {
            abort(403);
        }

        $shipment->load(['originPort.country', 'destinationPort.country']);

        return view('user.shipments.show', compact('shipment'));
    }

    /**
     * Remove the specified shipment from storage.
     */
    public function destroy(ImportShipment $shipment)
    {
        // Ensure user owns the shipment
        if ($shipment->user_id !== auth()->id()) {
            abort(403);
        }

        $shipment->delete();

        return redirect()->route('user.shipments.index')
            ->with('success', 'Rencana pengiriman barang berhasil dihapus.');
    }
}
