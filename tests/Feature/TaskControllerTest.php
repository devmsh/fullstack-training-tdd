<?php

namespace Tests\Feature;

use Database\Factories\TaskFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_tasks()
    {
        TaskFactory::new()->count(20)->create();

        $this->getJson('api/tasks')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'title',
                        'status',
                    ],
                ]
            ])
            ->assertJsonCount(15,'data')
            ->assertJson([
                'meta' => [
                    'total' => 20
                ]
            ]);
    }
}
