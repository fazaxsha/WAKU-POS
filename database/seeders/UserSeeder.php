<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Budi Santoso',
                'email'    => 'owner@toko.com',
                'password' => Hash::make('password'),
                'role'     => 'owner',
            ],
            [
                'name'     => 'Rina Wijaya',
                'email'    => 'admin@toko.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Doni Prasetyo',
                'email'    => 'kasir1@toko.com',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ],
            [
                'name'     => 'Sari Melati',
                'email'    => 'kasir2@toko.com',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => $data['password'],
                ]
            );

            $user->assignRole($data['role']);
        }

        $this->command->info('Users berhasil dibuat:');
        $this->command->table(
            ['Nama', 'Email', 'Role', 'Password'],
            collect($users)->map(fn($u) => [
                $u['name'], $u['email'], $u['role'], 'password'
            ])->toArray()
        );
    }
}