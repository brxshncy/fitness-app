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

     assignUserRole($this->user, Role::ADMIN);
});


test("admin can get lists of organization", function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($this->user);

    $response = $this->getJson(route('admin.organization.index'));

    dd($response->getContent()); 
});