<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ImportShipment;
use App\Services\RouteJourneyService;
use Illuminate\Http\JsonResponse;

class RouteJourneyController extends Controller
{
    protected RouteJourneyService $routeJourneyService;

    public function __construct(RouteJourneyService $routeJourneyService)
    {
        $this->routeJourneyService = $routeJourneyService;
    }

    /**
     * Display the Route Journey page.
     */
    public function show(ImportShipment $shipment)
    {
        // Ensure user owns the shipment
        if ($shipment->user_id !== auth()->id()) {
            abort(403);
        }

        $shipment->load(['originPort.country', 'destinationPort.country']);

        // Fetch all shipments for the sidebar list
        $allShipments = ImportShipment::with(['originPort.country', 'destinationPort.country'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.shipments.route', compact('shipment', 'allShipments'));
    }

    /**
     * Get route geometry and metadata via AJAX.
     */
    public function routeData(ImportShipment $shipment): JsonResponse
    {
        // Ensure user owns the shipment
        if ($shipment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $data = $this->routeJourneyService->getRouteData($shipment);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghitung rute perjalanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compute a sea route preview between two ports (by ID) without saving a shipment.
     * Used for live preview on the Shipments index page.
     */
    public function routePreview(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'origin_port_id'      => 'required|integer|exists:ports,id',
            'destination_port_id' => 'required|integer|exists:ports,id',
        ]);

        try {
            $origin      = \App\Models\Port::findOrFail($request->origin_port_id);
            $destination = \App\Models\Port::findOrFail($request->destination_port_id);

            $oLat = (float) $origin->latitude;
            $oLng = (float) $origin->longitude;
            $dLat = (float) $destination->latitude;
            $dLng = (float) $destination->longitude;

            // Use the same sea-corridor routing as the full shipment route
            $result = $this->routeJourneyService->computeSeaRoute($oLat, $oLng, $dLat, $dLng);

            return response()->json([
                'coordinates' => $result['coordinates'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghitung rute preview: ' . $e->getMessage()
            ], 500);
        }
    }
}
