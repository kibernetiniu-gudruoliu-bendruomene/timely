<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDate;
use App\Models\Date;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Meeting;

class DateController extends Controller
{

    public function store(StoreDate $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $meetingId = $request->input('meeting_id');

        $existingDatesCount = Date::where('meeting_id', $meetingId)->count();
        if ($existingDatesCount < 20) {
            $date = new Date;
            $date->meeting_id = $meetingId;
            $date->date_and_time = $validatedData['new_time'];
            $date->save();
        }

        return redirect("/meetings/$meetingId")->with('success', 'Date saved.');
    }

    public function update(StoreDate $request, $id): RedirectResponse
    {
        $validatedData = $request->validated();
        $date = Date::findOrFail($id);
        $meetingId = $date->meeting_id;
        if (Auth::check() && Meeting::where('user_id', Auth::user()->id)->exists($date->meeting_id)) {
            $date->date_and_time = $validatedData['new_time'];
            $date->save();
        }

        return redirect("/meetings/$meetingId")->with('success', 'Date updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $date = Date::findOrFail($id);
        if (Auth::check() && Meeting::where('user_id', Auth::user()->id)->exists($date->meeting_id)) {
            $meetingId = $date->meeting_id;

            $date->delete();
        }
        return redirect("/meetings/$meetingId")->with('error', 'Date deleted.');
    }
    public function select($id)
{
    $date = Date::findOrFail($id);

    // Get the meeting associated with the date
    $meeting = $date->meeting;

    // Update the selected value for all dates associated with the meeting
    $meeting->dates()->update(['selected' => 0]);
    $date->update(['selected' => 1]);

    return response()->json(['message' => 'Date selected successfully']);
}
}
