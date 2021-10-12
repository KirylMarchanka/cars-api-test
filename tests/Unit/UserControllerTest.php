<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeader('Accept', 'application/json');
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testUserSuccessfulSignup()
    {
        $this->post('/api/signup', [
            'name' => 'Test Signup',
            'email' => 'test.signup@example.net',
            'password' => 'password'
        ])
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email'
                ]
            ]);
    }

    public function testUserSignupWithIncorrectData()
    {
        $this->post('/api/signup', [
            'name' => 'Test Signup',
            'email' => 'test.signup@example.net',
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'password'
                ]
            ]);
    }

    public function testUserSignupWithExistsEmail()
    {
        $user = User::factory()->make();

        $this->post('/api/signup', [
            'name' => 'Test Signup',
            'email' => $user->name,
            'password' => 'password',
        ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);
    }

    public function testUserSuccessfulLogin()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'email' => $user->email
        ]);

        $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token'
                ]
            ]);
    }

    public function testUserLoginWithIncorrectData()
    {
        $user = User::factory()->create();

        $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'incorrectPassword',
        ])
            ->assertUnauthorized()
            ->assertJsonStructure([
                'message',
            ]);
    }
}
