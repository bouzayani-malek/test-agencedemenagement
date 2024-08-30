<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ReservationResource;
use Illuminate\Support\Facades\Auth;
class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $reservations = [];
        if (!Auth::user()->hasRole('admin')) {
            $reservations = $this->reservations()->where('user_id',Auth::user()->id)->get(); 
        }
        if (Auth::user()->hasRole('admin')) {
            $reservations = $this->reservations; 
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
            'category' => $this->category->name,
            'nombre_reservation' => count($reservations) ,
            'reservations' => ReservationResource::collection($reservations),
            'created_at' => $this->created_at?->toDateTimeString() ?? '',
        ];
    }
}
