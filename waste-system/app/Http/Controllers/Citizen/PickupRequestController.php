<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePickupRequestRequest;
use App\Jobs\SendPickupRequestConfirmation;
use App\Models\PickupRequest;
use Illuminate\Support\Facades\Storage;

class PickupRequestController extends Controller
{
    public function index()
    {
        $requests = PickupRequest::where('citizen_id', (string) auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('citizen.dashboard', compact('requests'));
    }

    public function create()
    {
        return view('citizen.requests.create');
    }

    public function store(CreatePickupRequestRequest $request)
    {
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('pickup-photos', 'public');
        }

        $pickupRequest = PickupRequest::create([
            'citizen_id' => (string) auth()->id(),
            'location' => [
                'type' => 'Point',
                'coordinates' => [(float) $request->longitude, (float) $request->latitude],
            ],
            'address' => $request->address,
            'waste_type' => $request->waste_type,
            'segregation_status' => 'pending_review',
            'status' => 'pending',
            'scheduled_at' => null,
            'photo_path' => $photoPath,
            'notes' => $request->notes,
        ]);

        SendPickupRequestConfirmation::dispatch((string) $pickupRequest->id);

        return redirect()
            ->route('citizen.requests.show', $pickupRequest->id)
            ->with('status', 'Pickup request submitted.');
    }

    public function show(string $id)
    {
        $pickupRequest = PickupRequest::findOrFail($id);

        if ($pickupRequest->citizen_id !== (string) auth()->id()) {
            abort(403);
        }

        return view('citizen.requests.show', compact('pickupRequest'));
    }
}
