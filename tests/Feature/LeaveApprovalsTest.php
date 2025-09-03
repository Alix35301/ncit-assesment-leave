<?php

namespace Tests\Feature;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveApprovalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_approve_leave_request()
    {
        $employee = User::factory()->create();
        $approver = User::factory()->create([
            'is_admin' => true,
        ]);

        $leave = Leave::create([
            'user_id' => $employee->id,
            'status' => 'pending',
            'type' => 'annual',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-01',
            'reason' => 'Test',

        ]);

        $response = $this->actingAs($approver)
            ->get(route('leave-approvals.approve', $leave));

        $leave->refresh();
        $this->assertEquals('approved', $leave->status);
        $this->assertEquals($approver->id, $leave->approved_by);
        $this->assertNotNull($leave->approved_at);

        $response->assertRedirect(route('leave-approvals.index'));
    }
}
