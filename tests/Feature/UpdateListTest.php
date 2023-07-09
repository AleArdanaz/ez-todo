<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lists;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateListTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_tasks_with_empty_params(): void
    {
        $user = User::factory()->create();
        $list = Lists::create([
            'name' => 'test',
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->post("/tasks/$list->id/update", [
            
        ]);
        $response->assertStatus(422);
    }
    /**
     * Test if a user that doesn't belong to the list can update it.
     */
    public function test_update_tasks_of_the_user(): void
    {

        $user = User::factory()->create();
        $list = Lists::create([
            'name' => 'test',
            'user_id' => $user->id,
        ]);
        
        $this->actingAs($user);
        $response = $this->post("/tasks/$list->id/update", [
            'list' => [
                'id' => $list->id,
                'name' => 'test',
                'tasks' => [
                    [
                        'name' => 'test 212121',
                        'order' => 1,
                        'completed' => false,
                        'created_at' => '2021-08-16T15:00:00.000000Z',
                        'updated_at' => '2021-08-16T15:00:00.000000Z',
                    ]
            ]],
        ]);
        
        $response->assertStatus(200);
    }

    /**
     * Test if a user that belongs to the list can update it.
     */
    public function test_update_tasks_of_another_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $list = Lists::create([
            'name' => 'test',
            'user_id' => $otherUser->id,
        ]);
        $this->actingAs($user);

        $response = $this->post("/tasks/$list->id/update", [
            'list' => [
                'id' => $list->id,
                'name' => 'test',
                'tasks' => [
                    [
                        'name' => 'test 212121',
                        'order' => 1,
                        'completed' => false,
                        'created_at' => '2021-08-16T15:00:00.000000Z',
                        'updated_at' => '2021-08-16T15:00:00.000000Z',
                    ]
            ]],
        ]);

        $response->assertStatus(403);
    }

}
