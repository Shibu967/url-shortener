<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement("
            INSERT INTO users (name, email, password, role, company_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            'Super Admin',
            'superadmin@example.com',
            Hash::make('password'),
            'SuperAdmin',
            null,
        ]);




     DB::statement("
    INSERT INTO companies (name, created_at, updated_at) 
    VALUES (?, NOW(), NOW()), 
           (?, NOW(), NOW()), 
           (?, NOW(), NOW()), 
           (?, NOW(), NOW()), 
           (?, NOW(), NOW())
", [
            'TechCorp India',
            'Digital Solutions Ltd',
            'Innovatech Systems',
            'NextGen Software',
            'Alpha Digital'
        ]);
    }
}
