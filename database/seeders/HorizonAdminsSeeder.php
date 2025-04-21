<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class HorizonAdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = explode(',', env('HORIZON_ALLOWED_EMAILS', ''));

        foreach ($emails as $email) {
            $email = trim($email);
            if (!$email) continue;

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Horizon Admin',
                    'password' => Hash::make('secret'),
                    'email_verified_at' => now(),
                    'email_verified' => true,
                    'role' => 'admin'
                ]
            );
        }

        $this->command->info('Horizon admin users seeded.');
    }
}
