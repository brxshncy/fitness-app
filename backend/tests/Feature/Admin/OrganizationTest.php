<?php

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    createRoles();
    
    $this->user = createUser([
        'name' => 'Admin',
        'email'    => 'admin@admin.com',
        'password' => Hash::make('admin123'),
    ]);

    $this->organization = createOrganization();
    assignUserRole($this->user, Role::ADMIN);
});

test("admin can get all organization lists", function () {
    Sanctum::actingAs($this->user);

    createOrganization([], 10);

    $response = $this->getJson(route("admin.organization.index"));

    $response->assertStatus(200);

    $response->assertJsonStructure(['data' => [['id', 'name', 'address']]]);

});

test("admin can create organization", function () {
    
    Sanctum::actingAs($this->user);

    $response = $this->postJson(route('admin.organization.store'), [
        'name' => 'Test Name',
        'address' => 'Test Address'
    ]);

    $response->assertStatus(201);

    $response->assertJsonStructure(['data' => ['id', 'name', 'address']]);

    $this->assertDatabaseHas('organizations', [
        'name' => 'Test Name', 
        'address' => 'Test Address'
    ]);
});

test('admin can update organization', function () {
    Sanctum::actingAs($this->user);

    $response = $this->putJson(route('admin.organization.update', $this->organization->id), [
        'name' => 'Updated Name',
        'address' => 'Updated Address'
    ]);

    $response->assertStatus(201);

    $response->assertJsonStructure(['data' => [
        'id',
        'name',
        'address',
        'created_at',
        'updated_at',
    ]]);

    $response->assertJsonFragment([
        'name' => 'Updated Name',
        'address' => 'Updated Address',
    ]);

    $this->assertDatabaseHas('organizations', [
        'id' => $this->organization->id,
        'name' => 'Updated Name',
        'address' => 'Updated Address',
    ]);

    $this->assertDatabaseMissing('organizations', [
        'id' => $this->organization->id,
        'name' => $this->organization->name, // old value
    ]);
});

test('admin can remove Organization', function () {
    Sanctum::actingAs($this->user);

    $response = $this->deleteJson(route('admin.organization.destroy', $this->organization->id));

    $response->assertJsonFragment(['message' => 'Organization removed']);

     $this->assertDatabaseMissing('organizations', [
        'id' => $this->organization->id,
        'name' => $this->organization->name, // old value
    ]);
});