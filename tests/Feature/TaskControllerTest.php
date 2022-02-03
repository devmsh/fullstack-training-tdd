<?php

namespace Tests\Feature;

use App\Models\Task;
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
                ],
            ])
            ->assertJsonCount(15, 'data')
            ->assertJson([
                'meta' => [
                    'total' => 20,
                ],
            ]);
    }

    public function test_can_create_new_tasks()
    {
        $this->postJson('api/tasks', [
            'title' => "example task",
        ])->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'example task',
                    'status' => false,
                ]
            ]);
    }

    public function test_task_is_incompleted_by_default()
    {
        $this->postJson('api/tasks', [
            'title' => "example task",
            'status' => true,
        ])->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'example task',
                    'status' => false,
                ]
            ]);
    }

    public function test_task_title_is_requiered()
    {
        $this->postJson('api/tasks', [
            'title1' => "example task",
        ])->assertJsonValidationErrorFor('title');
    }

    public function test_can_update_current_task()
    {
        TaskFactory::new()->create([
            'title' => 'old title',
            'status' => false,
        ]);

        $this->putJson('api/tasks/1', [
            'title' => "new title",
            'status' => true,
        ])->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'new title',
                    'status' => true,
                ]
            ]);
    }

    public function test_can_update_completed_task()
    {
        TaskFactory::new()->create([
            'title' => 'old title',
            'status' => true,
        ]);

        $this->putJson('api/tasks/1', [
            'title' => "new title",
            'status' => false,
        ])->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => 'new title',
                    'status' => false,
                ]
            ]);
    }

    public function test_can_delete_current_task()
    {
        TaskFactory::new()->create([
            'title' => 'old title',
            'status' => false,
        ]);

        $this->deleteJson('api/tasks/1')
            ->assertSuccessful();

        $this->assertDatabaseCount('tasks', 0);
    }
}
