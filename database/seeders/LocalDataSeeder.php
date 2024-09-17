<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityType;
use Illuminate\Database\Seeder;

class LocalDataSeeder extends Seeder
{
    public function run(): void
    {
        $numberOfUsers = 10;

        $utilityTypeIds = UtilityType::query()->pluck('id');

        User::factory()
            ->count($numberOfUsers)
            ->create([
                'status' => 'Active',
            ])
            ->each(function ($user) use ($utilityTypeIds) {
                $this->createCustomerAndMeters($user, $utilityTypeIds);
            });
    }

    protected function createCustomerAndMeters(User $user, $utilityTypeIds): void
    {
        $maxNumberOfMeters = 10;
        $customer = Customer::factory()
            ->for($user)
            ->create(['status' => 'Active']);

        Meter::factory()
            ->count(rand(1, $maxNumberOfMeters))
            ->for($customer)
            ->create([
                'utility_type_id' => $utilityTypeIds->random(),
                'tenant_id' => Tenant::factory()->create([
                    'customer_id' => $customer->id,
                    'user_id' => [null, User::factory()->create()->id][rand(0, 1)],
                ])->id,
                'status' => 'Active',
            ])
            ->each(function ($meter) {
                $this->createHourlyReadingsForYear($meter);
            });
    }

    protected function createHourlyReadingsForYear($meter): void
    {
        $startOfYear = now()->startOfYear();
        $now = now();
        $readings = [];

        while ($startOfYear->lessThanOrEqualTo($now)) {
            $readings[] = [
                'meter_id' => $meter->id,
                'value' => rand(0, 100),
                'reading_date' => $startOfYear->copy(),
                'source' => 'Generated',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $startOfYear->addHour();
        }

        $batchSize = 100;
        $chunks = array_chunk($readings, $batchSize);

        foreach ($chunks as $chunk) {
            MeterReading::query()->insert($chunk);
        }
    }
}
