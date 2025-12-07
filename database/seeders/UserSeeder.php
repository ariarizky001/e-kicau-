<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KelasLomba;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if users already exist
        if (User::where('email', 'superadmin@example.com')->exists()) {
            $this->command->warn('Super Admin user already exists!');
        } else {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Super Admin created successfully!');
        }

        if (User::where('email', 'admin@example.com')->exists()) {
            $this->command->warn('Admin user already exists!');
        } else {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Admin created successfully!');
        }

        // Create sample kelas lomba
        if (!KelasLomba::exists()) {
            for ($i = 1; $i <= 5; $i++) {
                KelasLomba::create([
                    'nomor_kelas' => $i,
                    'nama_kelas' => 'Kelas ' . chr(64 + $i),
                    'status' => 'aktif',
                    'batas_peserta' => 16,
                ]);
            }
            $this->command->info('Sample kelas lomba created successfully!');
        } else {
            $this->command->warn('Kelas lomba already exists!');
        }

        $this->command->newLine();
        $this->command->info('=== Login Credentials ===');
        $this->command->info('Super Admin:');
        $this->command->line('  Email: superadmin@example.com');
        $this->command->line('  Password: password');
        $this->command->newLine();
        $this->command->info('Admin:');
        $this->command->line('  Email: admin@example.com');
        $this->command->line('  Password: password');
    }
}

