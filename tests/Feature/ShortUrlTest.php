<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_short_url()
    {
        $company = Company::create(['name' => 'Test Company']);
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $response->assertRedirect('/short-urls');
        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://example.com',
            'user_id' => $admin->id,
            'company_id' => $company->id,
        ]);
    }

    public function test_member_can_create_short_url()
    {
        $company = Company::create(['name' => 'Test Company']);
        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@test.com',
            'password' => bcrypt('password'),
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        $this->actingAs($member);

        $response = $this->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $response->assertRedirect('/short-urls');
        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://example.com',
            'user_id' => $member->id,
        ]);
    }

    public function test_superadmin_cannot_create_short_url()
    {
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        $this->actingAs($superadmin);

        $response = $this->post('/short-urls', [
            'original_url' => 'https://example.com',
        ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseMissing('short_urls', [
            'original_url' => 'https://example.com',
        ]);
    }

    public function test_superadmin_can_see_all_short_urls()
    {
        $company1 = Company::create(['name' => 'Company 1']);
        $company2 = Company::create(['name' => 'Company 2']);

        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@test.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company1->id,
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
            'role' => 'Member',
            'company_id' => $company2->id,
        ]);

        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        ShortUrl::create([
            'original_url' => 'https://example1.com',
            'short_code' => 'abc12345',
            'user_id' => $user1->id,
            'company_id' => $company1->id,
        ]);

        ShortUrl::create([
            'original_url' => 'https://example2.com',
            'short_code' => 'def67890',
            'user_id' => $user2->id,
            'company_id' => $company2->id,
        ]);

        $this->actingAs($superadmin);

        $response = $this->get('/short-urls');

        $response->assertStatus(200);
        $response->assertSee('https://example1.com');
        $response->assertSee('https://example2.com');
    }

    public function test_admin_can_only_see_short_urls_from_other_companies()
    {
        $company1 = Company::create(['name' => 'Company 1']);
        $company2 = Company::create(['name' => 'Company 2']);

        $admin1 = User::create([
            'name' => 'Admin 1',
            'email' => 'admin1@test.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company1->id,
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
            'role' => 'Member',
            'company_id' => $company2->id,
        ]);

        ShortUrl::create([
            'original_url' => 'https://company1.com',
            'short_code' => 'comp1url',
            'user_id' => $admin1->id,
            'company_id' => $company1->id,
        ]);

        ShortUrl::create([
            'original_url' => 'https://company2.com',
            'short_code' => 'comp2url',
            'user_id' => $user2->id,
            'company_id' => $company2->id,
        ]);

        $this->actingAs($admin1);

        $response = $this->get('/short-urls');

        $response->assertStatus(200);
        $response->assertSee('https://company2.com');
        $response->assertDontSee('https://company1.com');
    }

    public function test_member_can_only_see_short_urls_not_created_by_themselves()
    {
        $company = Company::create(['name' => 'Test Company']);

        $member1 = User::create([
            'name' => 'Member 1',
            'email' => 'member1@test.com',
            'password' => bcrypt('password'),
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        $member2 = User::create([
            'name' => 'Member 2',
            'email' => 'member2@test.com',
            'password' => bcrypt('password'),
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        ShortUrl::create([
            'original_url' => 'https://member1.com',
            'short_code' => 'mem1url',
            'user_id' => $member1->id,
            'company_id' => $company->id,
        ]);

        ShortUrl::create([
            'original_url' => 'https://member2.com',
            'short_code' => 'mem2url',
            'user_id' => $member2->id,
            'company_id' => $company->id,
        ]);

        $this->actingAs($member1);

        $response = $this->get('/short-urls');

        $response->assertStatus(200);
        $response->assertSee('https://member2.com');
        $response->assertDontSee('https://member1.com');
    }

    public function test_short_url_is_publicly_resolvable()
    {
        $company = Company::create(['name' => 'Test Company']);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $shortUrl = ShortUrl::create([
            'original_url' => 'https://example.com',
            'short_code' => 'testcode',
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        $response = $this->get('/s/' . $shortUrl->short_code);

        $response->assertRedirect('https://example.com');
    }
}
