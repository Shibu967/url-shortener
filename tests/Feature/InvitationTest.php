<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_invite_admin_to_new_company()
    {
        $company = Company::create(['name' => 'New Company']);
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'SuperAdmin',
            'company_id' => null,
        ]);

        $this->actingAs($superadmin);

        $response = $this->post('/invitations', [
            'email' => 'newadmin@test.com',
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $response->assertRedirect('/invitations/create');
        $this->assertDatabaseHas('invitations', [
            'email' => 'newadmin@test.com',
            'role' => 'Admin',
            'company_id' => $company->id,
            'invited_by' => $superadmin->id,
        ]);
    }

    public function test_admin_can_invite_admin_to_own_company()
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

        $response = $this->post('/invitations', [
            'email' => 'newadmin@test.com',
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $response->assertRedirect('/invitations/create');
        $this->assertDatabaseHas('invitations', [
            'email' => 'newadmin@test.com',
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);
    }

    public function test_admin_can_invite_member_to_own_company()
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

        $response = $this->post('/invitations', [
            'email' => 'newmember@test.com',
            'role' => 'Member',
            'company_id' => $company->id,
        ]);

        $response->assertRedirect('/invitations/create');
        $this->assertDatabaseHas('invitations', [
            'email' => 'newmember@test.com',
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
    }

    public function test_invitation_can_be_accepted()
    {
        $company = Company::create(['name' => 'Test Company']);
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company->id,
        ]);

        $invitation = Invitation::create([
            'email' => 'newuser@test.com',
            'role' => 'Member',
            'company_id' => $company->id,
            'invited_by' => $admin->id,
            'token' => 'testtoken123',
        ]);

        $response = $this->post('/invite/accept/testtoken123', [
            'name' => 'New User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com',
            'role' => 'Member',
            'company_id' => $company->id,
        ]);
        $this->assertNotNull(Invitation::find($invitation->id)->accepted_at);
    }
}
