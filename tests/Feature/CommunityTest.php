<?php

namespace Tests\Feature;

use App\Group;
use App\Page;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::create([
            'first_name' => 'Community',
            'last_name' => 'Member',
            'email' => 'community@example.com',
            'password' => Hash::make('secret123'),
        ]);
    }

    public function test_community_page_requires_authentication(): void
    {
        $this->get('/community')->assertRedirect('/login');
        $this->post('/pages', ['name' => 'Unauthorized'])->assertRedirect('/login');
    }

    public function test_member_can_create_a_page_and_group_owned_by_them(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post('/pages', [
            'name' => 'My Community Page',
            'description' => 'News and updates',
        ])->assertRedirect();

        $this->actingAs($user)->post('/groups', [
            'name' => 'My Community Group',
            'description' => 'A place to talk',
        ])->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'name' => 'My Community Page',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('groups', [
            'name' => 'My Community Group',
            'user_id' => $user->id,
        ]);
    }

    public function test_community_creation_validates_lengths_and_keeps_errors_separate(): void
    {
        $user = $this->user();
        $this->actingAs($user)->from('/community')->post('/pages', [
            'name' => str_repeat('x', 121),
            'description' => str_repeat('y', 501),
        ])->assertRedirect('/community')->assertSessionHasErrorsIn('page', ['name', 'description']);

        $this->actingAs($user)->from('/community')->post('/groups', [
            'name' => '',
        ])->assertRedirect('/community')->assertSessionHasErrorsIn('group', ['name']);

        $this->assertSame(0, Page::count());
        $this->assertSame(0, Group::count());
    }

    public function test_community_lists_are_paginated_and_show_the_owner(): void
    {
        $user = $this->user();
        foreach (range(1, 13) as $number) {
            Page::create(['user_id' => $user->id, 'name' => 'Community page '.$number]);
            Group::create(['user_id' => $user->id, 'name' => 'Community group '.$number]);
        }

        $this->actingAs($user)->get('/community')
            ->assertOk()
            ->assertSee('Community page 13')
            ->assertDontSee('Community page 1</h4>', false)
            ->assertSee('بواسطة Community Member')
            ->assertSee('Pagination');

        $this->get('/community?pages_page=2&groups_page=2')
            ->assertOk()
            ->assertSee('Community page 1</h4>', false)
            ->assertSee('Community group 1</h4>', false);
    }
}
