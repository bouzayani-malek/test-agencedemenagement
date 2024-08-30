<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReservationResource;
use App\Custom\CustomPaginator;
use App\Services\ReservationService;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('view reservations')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        try {

            $validated = $request->validate([
                'per_page' => 'sometimes|integer|min:1',
                'user_id' => 'sometimes|integer|min:1',
                'event_id' => 'sometimes|integer|min:1',
                'status' => 'sometimes|integer|min:1|max:5',
            ]);

            $perPage = $request->query('per_page', 10);
            $userId = $request->query('user_id', Auth::user()->hasRole('admin') ? '' : Auth::user()->id);
            $eventId = $request->query('event_id', '');
            $status = $request->query('status', '');

            $reservations = $this->reservationService->getReservations($perPage, $userId, $eventId, $status);
            $customPaginator = new CustomPaginator(
                ReservationResource::collection($reservations->items()),
                $reservations->total(),
                $reservations->perPage(),
                $reservations->currentPage()
            );
            return response()->json($customPaginator);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function reserver(Request $request)
    {
        if (Auth::user()->cannot('create reservations')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        try { 
            $validated = $request->validate([
                'user_id' => 'required|integer|min:1',
                'event_id' => 'required|integer|min:1',
            ]);

            $userId = Auth::user()->hasRole('admin') ? $validated['user_id'] : Auth::user()->id;
            $eventId = $validated['event_id'];

            if ($this->reservationService->checkExistingReservation($userId, $eventId)) {
                return response()->json(['message' => 'Vous êtes déjà inscrit à cet événement'], 200);
            }

            $reservation = $this->reservationService->createReservation($userId, $eventId);
            return response()->json(new ReservationResource($reservation), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateEtat(Request $request)
    {
        if (Auth::user()->cannot('update reservations')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'status' => 'required|integer|min:1|max:5',
                'id' => 'required|integer|min:1',
            ]);

            $reservation = $this->reservationService->getReservationById($request->id);

            if (!$reservation) {
                return response()->json(['message' => 'Reservation not found'], 404);
            }

            if (!Auth::user()->hasRole('admin') && $reservation->user_id !== Auth::user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $reservation = $this->reservationService->updateReservationStatus($request->id, $request->status);
            return response()->json(new ReservationResource($reservation), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
