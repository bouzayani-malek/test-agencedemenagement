<?php

namespace App\Services;

use App\Models\Event;

class EventService
{
    public function getEvents($perPage, $search, $category)
    {
        $query = Event::where('is_deleted', 0);

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%");
        }

        if ($category) {
            $query->where('category_id', $category);
        }
        return $query->paginate($perPage);
    }

    public function createEvent(array $data)
    {
        return Event::create($data);
    }

    public function updateEvent($id, array $data)
    {
        $event = Event::where('id', $id)->where('is_deleted', 0)->first();

        if (!$event) {
            return null;
        }

        $event->update($data);
        return $event;
    }

    public function deleteEvent($id)
    {
        $event = Event::where('id', $id)->where('is_deleted', 0)->first();

        if (!$event) {
            return false;
        }

        $event->is_deleted = 1;
        $event->save();
        return true;
    }

    public function getEventById($id)
    {
        return Event::where('id', $id)->where('is_deleted', 0)->first();
    }
}
