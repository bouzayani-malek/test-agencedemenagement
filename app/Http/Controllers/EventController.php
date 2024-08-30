<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\EventResource;
use App\Custom\CustomPaginator;
use App\Services\EventService;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('view events')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        try {

            $validated = $request->validate([
                'per_page' => 'sometimes|integer|min:1',
                'search' => 'sometimes|string|max:255',
                'category' => 'sometimes|integer|min:1',
            ]);
    
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search', '');
            $category = $request->query('category'); 

            $events = $this->eventService->getEvents($perPage, $search, $category);
            $customPaginator = new CustomPaginator(
                EventResource::collection($events->items()),
                $events->total(),
                $events->perPage(),
                $events->currentPage()
            );
            return response()->json($customPaginator);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin') || Auth::user()->cannot('create events')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        try { 

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|integer',
                'location' => 'required|string|max:255',
                'category_id' => 'required|integer|min:1',
            ]);

            $event = $this->eventService->createEvent($validated);
            return response()->json(new EventResource($event), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('admin') || Auth::user()->cannot('update events')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try { 
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|integer',
                'location' => 'required|string|max:255',
                'category_id' => 'required|integer|min:1',
            ]);

            $event = $this->eventService->updateEvent($id, $validated);

            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            return response()->json(new EventResource($event), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin') || Auth::user()->cannot('delete events')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        try{
            $isDeleted = $this->eventService->deleteEvent($id);

            if (!$isDeleted) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            return response()->json(['message' => 'Event deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        if (Auth::user()->cannot('view events')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $event = $this->eventService->getEventById($id);

            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            return response()->json(new EventResource($event), 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
