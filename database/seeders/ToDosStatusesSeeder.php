<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ToDosStatuses;
class TodosStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ToDosStatuses = ['todo', 'done'];

        ToDosStatuses::factory()
            ->count(count($ToDosStatuses))
            ->sequence(fn ($sequence) => ['name' => $ToDosStatuses[$sequence->index]])
            ->create();
        
    }
}
