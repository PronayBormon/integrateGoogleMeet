<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Services\GoogleService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class MeetingController extends Controller
{
    public function store(Request $request, GoogleService $google)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $client = $google->client(auth()->id());
        if (!$client->getAccessToken()) {
            return redirect()->route('google.connect')->with('error', 'Connect Google first');
        }

        // dd($request->all());
        $service = new Google_Service_Calendar($client);
        
        $userTimeZone = $request->timezone;

        $start = \Carbon\Carbon::parse($request->start_time, $userTimeZone)
            ->setTimezone('UTC')
            ->toIso8601String();

        $end = \Carbon\Carbon::parse($request->end_time, $userTimeZone)
            ->setTimezone('UTC')
            ->toIso8601String();
        $event = new Google_Service_Calendar_Event([
            'summary' => $request->title,
            'description' => $request->description ?? '',
            'start' => [
                'dateTime' => $start,
                'timeZone' => $userTimeZone
            ],
            'end' => [
                'dateTime' => $end,
                'timeZone' => $userTimeZone
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid('meet_'),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ]
            ],
            'attendees' => [
                ['email' => auth()->user()->email],
                ['email' => \App\Models\User::find($request->student_id)->email]
            ]
        ]);


        // dd($event);

        $created = $service->events->insert(
            'primary',
            $event,
            ['conferenceDataVersion' => 1] // << REQUIRED for creating Meet links. See note below.
        );

        $meeting = Meeting::create([
            'tutor_id' => auth()->id(),
            'student_id' => $request->student_id,
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'google_event_id' => $created->getId(),
            'meet_link' => $created->hangoutLink ?? ($created->getConferenceData()->getEntryPoints()[0]->uri ?? null)
        ]);

        // Optional: email notification to student (Mail::to(...)->send(new MeetingMail($meeting)))

        // return response()->json($meeting);

        return redirect()->route('meetings.index')->with('success', 'Meeting scheduled. Meet link: ' . $meeting->meet_link);
    }

    public function meeting(Request $request)
    {
        $students = User::get();

        $meetings = Meeting::where('tutor_id', auth()->id())->orderBy('start_time', 'desc')->get();

        return view('meetings.create', compact('students', 'meetings'));
    }


}
