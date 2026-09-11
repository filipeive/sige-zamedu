<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EventNotification;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Filtro por escola (super admin / territorial)
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('audience')) {
            $query->where('target_audience', $request->audience);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->with('createdBy')->orderBy('event_date', 'desc')->paginate(10);
        $schools = School::orderBy('name')->get();

        return view('events.index', compact('events', 'schools'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'target_audience' => 'required|in:all,students,parents,teachers',
            'type' => 'required|in:meeting,celebration,exam,activity',
            'send_notification' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['send_notification'] = $request->has('send_notification');

        $event = Event::create($validated);

        if ($event->send_notification) {
            $this->sendNotification($event);
        }

        return redirect()->route('events.index')
            ->with('success', 'Evento criado com sucesso!');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'target_audience' => 'required|in:all,students,parents,teachers',
            'type' => 'required|in:meeting,celebration,exam,activity',
            'send_notification' => 'boolean',
        ]);

        $validated['send_notification'] = $request->has('send_notification');
        $event->update($validated);

        return redirect()->route('events.index')
            ->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Evento excluído com sucesso!');
    }

    /**
     * Display a calendar view of events.
     */
    public function calendar()
    {
        $events = Event::all()->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->event_date->format('Y-m-d') . 'T' . $event->start_time->format('H:i:s'),
                'end' => $event->event_date->format('Y-m-d') . 'T' . $event->end_time->format('H:i:s'),
                'url' => route('events.show', $event),
                'className' => 'event-' . $event->type,
            ];
        });

        return view('events.calendar', compact('events'));
    }

    /**
     * Send notification to the target audience.
     */
    public function sendNotification(Event $event)
    {
        $users = User::query();

        if ($event->target_audience !== 'all') {
            $users->role($event->target_audience === 'teachers' ? 'teacher' : ($event->target_audience === 'parents' ? 'parent' : 'student'));
        }

        $recipients = $users->get();

        Notification::send($recipients, new EventNotification($event));

        return back()->with('success', 'Notificações enviadas com sucesso!');
    }
}
