<?php

namespace App\Services;

use App\Models\Reservation;

class ReservationService
{
    public function getReservations($perPage, $userId, $eventId, $status)
    {
        $query = Reservation::where('is_deleted', 0);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function createReservation($userId, $eventId)
    {
        return Reservation::create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'status' => 1,
        ]);
    }

    public function updateReservationStatus($id, $status)
    {
        $reservation = Reservation::where('id', $id)->where('is_deleted', 0)->first();

        if (!$reservation) {
            return null;
        }

        $reservation->status = $status;
        $reservation->save();
        return $reservation;
    }

    public function getReservationById($id)
    {
        return Reservation::where('id', $id)->where('is_deleted', 0)->first();
    }

    public function checkExistingReservation($userId, $eventId)
    {
        return Reservation::where('user_id', $userId)
                          ->where('event_id', $eventId)
                          ->where('is_deleted', 0)
                          ->exists();
    }
}
