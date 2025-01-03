<?php

namespace Tests\Unit\Services;

use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    use RefreshDatabase;

    public UserService $user_service;

    //set up service
    protected function setUp(): void
    {
        parent::setUp();

        $this->user_service = new UserService();
    }

    //set up session from id
    private function storeRandomUserId()
    {
        $dummy_user_id = fake()->uuid();

        Session::put('user_id', $dummy_user_id);

        return $dummy_user_id;
    }

    // get id from session

    public function test_should_return_user_id_from_session()
    {
        $john_user_id = $this->storeRandomUserId();

        $this->assertSame($john_user_id, $this->user_service->getUserIdFromSession());

        $alice_user_id = $this->storeRandomUserId();

        $this->assertSame($alice_user_id, $this->user_service->getUserIdFromSession());
    }

    ///get user id even if regen sssion
    public function test_should_return_same_user_id_in_case_regenerate_session_id()
    {
        $john_user_id = $this->storeRandomUserId();

        $this->assertSame($john_user_id, $this->user_service->getUserIdFromSession());

        Session::regenerate();

        $this->assertSame($john_user_id, $this->user_service->getUserIdFromSession());
    }

    //get flush session then return new id;

    public function test_should_return_new_user_id_in_case_flush_session()
    {
        $john_user_id = $this->storeRandomUserId();

        $this->assertSame($john_user_id, $this->user_service->getUserIdFromSession());

        Session::flush();

        $this->assertNotSame($john_user_id, $this->user_service->getUserIdFromSession());
    }

}
