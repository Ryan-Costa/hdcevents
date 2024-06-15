<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $search = request('search');

        if($search) {

            $events = Event::where([
                ['title', 'like', '%'.$search.'%']
            ])->get();

        } else {
            $events = Event::all();
        }


        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    public function create() {
        return view('events.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'city' => 'required|string',
            'image' => 'required|image|max:2048',
        ]);

        $event = new Event;
        $event->title = $request->title;
        $event->date = $request->date;
        $event->description = $request->description;
        $event->city = $request->city;
        $event->private = $request->input('private') === 'on' ? 1 : 0;
        $event->items = $request->items;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now'))  . "." . $extension;

            $request->image->move(public_path('storage/events'), $imageName);

            $event->imageUrl = Storage::url("events/$imageName");
        }

        $user = auth()->user();
        $event->user_id = $user->id;

        $event->save();

//        return response()->json([
//            'status' => 'success',
//            'message' => 'evento criado com sucesso!',
//            'data' => $event,
//        ], 200);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    public function show($id) {
        $event = Event::findOrFail($id);

        $user = auth()->user();

        $hasUserJoined = false;

        if ($user) {
            $userEvents = $user->eventsAsParticipant->toArray();

            foreach($userEvents as $userEvent) {
                if($userEvent['id'] == $id) {
                    $hasUserJoined = true;
                }
            }
        }

        $eventOwner = User::where('id', $event->user_id)->first()->toArray();

        return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner, 'hasUserJoined' => $hasUserJoined]);

    }

    public function dashboard() {
        $user = auth()->user();

        $events = $user->events;

        $eventsAsParticipant = $user->eventsAsParticipant;

        return view('events.dashboard',
            ['events' => $events, 'eventsasparticipant' => $eventsAsParticipant]);
    }

    public function destroy($id) {
        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');
    }

    public function edit($id) {

        $user = auth()->user();

        $event = Event::findOrFail($id);

        if ($user->id !== $event->user_id) {
            return redirect('/dashboard');
        }

        return view('events.edit', ['event' => $event]);
    }

    public function update(Request $request) {
        $data = $request->all();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now'))  . "." . $extension;

            $request->image->move(public_path('storage/events'), $imageName);

            $data['imageUrl'] = Storage::url("events/$imageName");
        }

        $data['private'] = $request->input('private') === 'on' ? 1 : 0;

        Event::findOrFail($request->id)->update($data);

        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');
    }

    public function joinEvent($id) {
        $user = auth()->user();
        $user->eventsAsParticipant()->attach($id);
        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presença está confirmada no evento ' . $event->title . '!');
    }

    public function leaveEvent($id) {
        $user = auth()->user();
        $user->eventsAsParticipant()->detach($id);
        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Você não está mais participando do evento ' . $event->title . '!');
    }
}
