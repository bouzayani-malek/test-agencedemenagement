<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       // return parent::toArray($request);
       $status = '';
        switch ($this->status) {
            case 1:
                $status = 'en attente.';
                break;
            case 2:
                $status = 'en cours de traitement.';
                break;
            case 3:
                $status = 'annuler.';
                break;
            case 4:
                $status = 'accepter.';
                break;
            case 5:
                $status = 'refuser.';
                break;
            default:
                $status = 'inconnu';
                break;
        }
       return [
            'id' => $this->id,
            'user' => $this->user->name,
            'event' => $this->event->title,
            'status' => $status,
            'created_at' => $this->created_at?->toDateTimeString() ?? '',
        ];
    }
}
