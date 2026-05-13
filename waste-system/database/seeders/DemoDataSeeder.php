<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PickupRequest;
use App\Models\Role;
use App\Models\Route;
use App\Models\Truck;
use App\Models\User;
use App\Models\WasteLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PickupRequest::raw(fn($collection) => $collection->createIndex(['location' => '2dsphere']));

        $roles = [
            'citizen' => [
                'create-pickup-request',
                'view-own-requests',
                'track-request-status',
            ],
            'driver' => [
                'view-assigned-routes',
                'update-collection-status',
                'mark-waste-collected',
            ],
            'admin' => [
                'manage-all-requests',
                'assign-trucks-and-routes',
                'view-analytics',
                'manage-users',
                'export-reports',
            ],
        ];

        // Skip role/permission creation - use direct user role field instead

        $admins = collect();
        for ($i = 1; $i <= 3; $i += 1) {
            $admin = User::factory()->create([
                'name' => "Admin {$i}",
                'email' => "admin{$i}@waste.local",
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);
            $admins->push($admin);
        }

        $citizens = collect();
        $demoCitizen = User::factory()->create([
            'name' => 'Demo Citizen',
            'email' => 'citizen@waste.local',
            'role' => 'citizen',
            'password' => Hash::make('password'),
        ]);
        $citizens->push($demoCitizen);
        $citizens = $citizens->merge(User::factory()->count(9)->create(['role' => 'citizen']));

        $drivers = collect();
        $demoDriver = User::factory()->create([
            'name' => 'Demo Driver',
            'email' => 'driver@waste.local',
            'role' => 'driver',
            'password' => Hash::make('password'),
        ]);
        $drivers->push($demoDriver);
        $drivers = $drivers->merge(User::factory()->count(4)->create(['role' => 'driver']));

        $trucks = collect();
        foreach ($drivers as $driver) {
            $truck = Truck::factory()->create([
                'driver_id' => (string) $driver->id,
                'status' => 'available',
            ]);
            $trucks->push($truck);
        }

        $pickupRequests = collect();
        for ($i = 0; $i < 100; $i += 1) {
            $citizen = $citizens->random();
            $request = PickupRequest::factory()->make();
            $request->citizen_id = (string) $citizen->id;
            $request->save();
            $pickupRequests->push($request);
        }

        for ($i = 0; $i < 10; $i += 1) {
            $truck = $trucks->random();
            $admin = $admins->random();
            $stopRequests = $pickupRequests->shuffle()->take(6)->values();
            $stops = $stopRequests->map(function ($request, $index) {
                return [
                    'pickup_request_id' => (string) $request->id,
                    'order_index' => $index + 1,
                    'completed_at' => null,
                ];
            })->all();

            Route::factory()->create([
                'truck_id' => (string) $truck->id,
                'admin_id' => (string) $admin->id,
                'stops' => $stops,
                'status' => 'planned',
            ]);

            PickupRequest::whereIn('_id', $stopRequests->pluck('id')->all())
                ->update(['status' => 'assigned']);
        }

        $completedRequests = PickupRequest::where('status', 'completed')->get();
        foreach ($completedRequests as $request) {
            WasteLog::factory()->create([
                'pickup_request_id' => (string) $request->id,
                'segregation_compliant' => $request->segregation_status === 'compliant',
            ]);
        }
    }
}
