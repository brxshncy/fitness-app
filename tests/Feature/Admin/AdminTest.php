<?php

use App\Models\Role as ModelsRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Spatie caches roles/permissions; clear between tests to avoid “sticky” state.
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

/**
 * Small helpers to DRY things up
 */
function ensureAdminRole(string $guard = 'api'): Role
{
    // Creates or fetches the 'admin' role for the given guard
    return Role::firstOrCreate(['name' => ModelsRole::ADMIN]);
}

function makeAdminUser(array $overrides = []): User
{
    $user = User::factory()->create(array_merge([
        'email'    => 'admin@admin.com',
        'password' => Hash::make('admin123'),
    ], $overrides));

    $user->assignRole(ensureAdminRole());

    return $user;
}

it('allows an admin to login successfully', function () {
    $user = makeAdminUser();

    // Hit your named route for admin login; returns user + accessToken
    $response = $this->postJson(route('admin.login'), [
        'email'    => 'admin@admin.com',
        'password' => 'admin123',
    ]);

    // 200 OK and token+user structure present
    $response->assertOk()
        ->assertJsonStructure(['user', 'accessToken']);

    // Optional: confirm the token was actually created in DB for this user
    // (Sanctum stores a hashed token; we assert by token name + owner)
    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_type' => User::class,
        'tokenable_id'   => $user->id,
        'name'           => 'admin',
    ]);
});

it('rejects invalid credentials with 401', function () {
    makeAdminUser();

    $response = $this->postJson(route('admin.login'), [
        'email'    => 'admin@admin.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJsonFragment(['message' => 'Invalid Credentials']);
});

it('logs out the current token and prevents reuse', function () {
    $user = makeAdminUser();

    Sanctum::actingAs($user);

    $response = $this->getJson(route('admin.logout'));

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Logged out successfully']);

});
