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

        $permissionModels = [];
        foreach ($roles as $roleName => $permissions) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            foreach ($permissions as $permissionName) {
                $permissionModels[$permissionName] = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
            }
        }

        foreach ($roles as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            $role?->syncPermissions($permissions);
        }

        $admins = collect();
        for ($i = 1; $i <= 3; $i += 1) {
            $admin = User::factory()->create([
                'name' => "Admin {$i}",
                'email' => "admin{$i}@waste.local",
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);
            $admin->assignRole('admin');
            $admins->push($admin);
        }

        $citizens = User::factory()->count(10)->create(['role' => 'citizen']);
        $citizens->each->assignRole('citizen');

        $drivers = User::factory()->count(5)->create(['role' => 'driver']);
        $drivers->each->assignRole('driver');

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
