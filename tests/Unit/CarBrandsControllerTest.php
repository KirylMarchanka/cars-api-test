<?php

namespace Tests\Unit;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CarBrandsControllerTest extends TestCase
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
    }

    public function testTokenIsRequired()
    {
        $this->withHeader('Authorization', '');

        $this->get('/api/brands')->assertUnauthorized()->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testShowAllBrands()
    {
        $this->get('/api/brands')
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

    public function testSuccessfullyCreateBrand()
    {
        $this->post('/api/brands', [
            'name' => Str::random()
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

    public function testBrandCreateWithIncorrectData()
    {
        $this->post('/api/brands')
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                ]
            ]);
    }

    public function testSuccessfullyUpdateBrand()
    {
        $brand = CarBrand::factory()->create(['created_by' => $this->user->id]);

        $this->put('/api/brands/' . $brand->id, [
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

    public function testNotOwnerCantUpdateBrand()
    {
        $user = User::factory()->create();

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->getContent();

        $this->withToken(json_decode($token, true)['data']['token']);

        $brand = CarBrand::factory()->create([
            'created_by' => 1
        ]);

        $this->put('/api/brands/' . $brand->id)->assertForbidden()->assertJson([
            'message' => 'You can\'t work with this car brand'
        ]);
    }

    public function testBrandSuccessfullyDeleted()
    {
        $user = $this->user;

        $brand = CarBrand::factory()->create(['created_by' => $user->id]);

        $this->delete('/api/brands/' . $brand->id)->assertOk()->assertJson([
            'message' => 'Car brand successfully deleted'
        ]);
    }

    public function testNotOwnerCantDeleteBrand()
    {
        $user = User::factory()->create();

        $token = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->getContent();

        $this->withToken(json_decode($token, true)['data']['token']);

        $brand = CarBrand::factory()->create([
            'created_by' => 1
        ]);

        $this->delete('/api/brands/' . $brand->id)->assertForbidden()->assertJson([
            'message' => 'You can\'t work with this car brand'
        ]);
    }

    public function testBrandAndModelSearch()
    {
        $brand = CarBrand::factory()->create(['name' => 'Audi']);
        CarModel::factory()->create([
            'name' => 'A3',
            'brand_id' => $brand->id
        ]);

        $searchByBrand = $this->get('/api/brands/search?name=Audi')->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'created_by' => [
                    'id',
                    'name'
                ],
                'car_models' => [
                    '*' => [
                        'id',
                        'name',
                        'brand_id',
                        'created_by',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ]);
        $searchByModel = $this->get('/api/brands/search?name=A3')->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'created_by' => [
                    'id',
                    'name'
                ],
                'car_models' => [
                    '*' => [
                        'id',
                        'name',
                        'brand_id',
                        'created_by',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ]);

        $this->assertJsonStringEqualsJsonString($searchByBrand->getContent(), $searchByModel->getContent());
    }

    public function testBrandsInsertedByUser()
    {
        $this->get('/api/user/brands')->assertOk()->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_by' => [
                        'id',
                        'name'
                    ]
                ]
            ],
            'links'
        ])->assertJsonFragment([
            'created_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]
        ]);
    }
}
