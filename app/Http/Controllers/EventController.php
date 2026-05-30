<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $search = request("search");

        $events = $search
            ? Event::where("title", "like", "%" . $search . "%")->get()
            : Event::all();

        return view('welcome', [
            'events' => $events,
            'search' => $search
        ]);
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $event = new Event;

        $event->title = $request->title;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->date = $request->date;
        $event->items = $request->items ? $request->items : [];
        $event->user_id = Auth::id();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $name = md5($image->getClientOriginalName() . time()) . "." . $image->extension();
            $image->move(public_path('img/events'), $name);
            $event->image = $name;
        }

        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        $eventOwner = User::find($event->user_id);
        $user = Auth::user();
        $hasUserJoined = false;

        if ($user) {
            $userEvents = $user->eventsAsParticipant->toArray();

            foreach($userEvents as $userEvent) {
                if($userEvent["id"] == $id) {
                    $hasUserJoined = true;
                }
            }
        }

        return view('events.show', [
            'event' => $event,
            'eventOwner' => $eventOwner,
            "hasUserJoined" => $hasUserJoined
        ]);
    }

    public function dashboard()
    {
        $events = Event::where('user_id', Auth::id())->get();

        $eventsAsParticipant = Auth::user()->eventsAsParticipant;

        return view('events.dashboard', [
            'events' => $events,
            'eventsasparticipant' => $eventsAsParticipant
        ]);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->user_id != Auth::id()) {
            return redirect('/dashboard');
        }

        $event->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);

        if ($event->user_id != Auth::id()) {
            return redirect('/dashboard');
        }

        return view('events.edit', [
            'event' => $event
        ]);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if ($event->user_id != Auth::id()) {
            return redirect('/dashboard');
        }

        $data = $request->validate([
            'title' => 'nullable|string|max:250',
            'date' => 'nullable|date',
            'city' => 'nullable|string|max:250',
            'private' => 'nullable|boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $data['items'] = $request->items ? $request->items : [];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $name = md5($image->getClientOriginalName() . time()) . "." . $image->extension();
            $image->move(public_path('img/events'), $name);
            $data['image'] = $name;
        }

        $event->update($data);

        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');
    }

    public function joinEvent($id)
    {
        $event = Event::findOrFail($id);

        Auth::user()->eventsAsParticipant()->syncWithoutDetaching([$event->id]);

        return redirect('/dashboard')->with('msg', 'Você entrou no evento!');
    }

    public function leaveEvent($id)
    {
        $event = Event::findOrFail($id);

        Auth::user()->eventsAsParticipant()->detach($event->id);

        return redirect('/dashboard')->with('msg', 'Você saiu do evento!');
    }
}