<?php

namespace Tests\Unit;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CarModelsControllerTest extends TestCase
{
    use RefreshDatabase;

    private Model $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeader('Accept', 'application/json');

        $this->user = User::factory()->create();

        $token = $this->post('/api/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ])->getContent();

        $this->withToken(json_decode($token, true)['data']['token']);

        CarBrand::factory()->count(10)->create();
        CarModel::factory()->count(10)->create();
    }

    public function testTokenIsRequired()
    {
        $this->withHeader('Authorization', '');

        $this->get('/api/models')->assertUnauthorized()->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function testShowAllModels()
    {
        $this->get('/api/models')
            ->assertOk()
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_by'
                    ]
                ],
                'links'
            ]);
    }

    public function testSuccessfullyCreateModel()
    {
        $this->post('/api/models', [
            'name' => Str::random(),
            'brand_id' => rand(1, CarBrand::count())
        ])
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'created_by'
                ]
            ]);
    }

    public function testModelCreateWithIncorrectData()
    {
        $this->post('/api/models')
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                ]
            ]);
    }

    public function testSuccessfullyUpdateModel()
    {
        $model = CarModel::factory()->create(['created_by' => $this->user->id]);

        $this->put('/api/models/' . $model->id, [
            'name' => Str::random()
        ])
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'created_by'
                ]
            ]);
    }

    public function testNotOwnerCantUpdateModel()
    {
        $user = User::factory()->create();

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->getContent();

        $this->withToken(json_decode($token, true)['data']['token']);

        $model = CarModel::factory()->create([
            'created_by' => 1
        ]);

        $this->put('/api/models/' . $model->id)->assertForbidden()->assertJson([
            'message' => 'You can\'t work with this car model'
        ]);
    }

    public function testModelSuccessfullyDeleted()
    {
        $user = $this->user;

        $model = CarModel::factory()->create(['created_by' => $user->id]);

        $this->delete('/api/models/' . $model->id)->assertOk()->assertJson([
            'message' => 'Car model successfully deleted'
        ]);
    }

    public function testNotOwnerCantDeleteModel()
    {
        $user = User::factory()->create();

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->getContent();

        $this->withToken(json_decode($token, true)['data']['token']);

        $brand = CarModel::factory()->create([
            'created_by' => 1
        ]);

        $this->delete('/api/models/' . $brand->id)->assertForbidden()->assertJson([
            'message' => 'You can\'t work with this car model'
        ]);
    }
}
