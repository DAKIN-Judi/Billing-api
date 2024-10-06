<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    protected function getAuthToken()
    {
        $user = User::factory()->create();
        return JWTAuth::fromUser($user);
    }
}
