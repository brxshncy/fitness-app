<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});


test("admin can get lists of organization", function () {
    $this->withoutExceptionHandling(); 
    $response = $this->get(route('admin.organization.index'));

    $response->dd(); 
});