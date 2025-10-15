<?php

namespace Database\Seeders;

use App\Models\InternalUser;
use Illuminate\Database\Seeder;

class InternalUserSeeder extends Seeder
{
    public function run(): void
    {
        InternalUser::updateOrCreate(
            ['email' => 'marcelo.dias@etikasolucoes.com.br'],
            [
                'name' => 'Marcelo',
                'role' => InternalUser::ROLE_ADMIN,
                'password' => '123456',
            ]
        );
    }
}
