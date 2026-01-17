<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Beneficiary
        Role::firstOrCreate(['name' => 'beneficiary'])
            ->syncPermissions(Permission::where('name','like','beneficiary.%')
                ->orWhere('name','like','assessment%')
                ->orWhere('name','like','activity.%')
                ->orWhere('name','like','case.%self%')
                ->get()
            );

        // Specialist
        Role::firstOrCreate(['name' => 'specialist'])
            ->syncPermissions([
                'file.read','file.update',
                'sessions.create','sessions.read','sessions.update','sessions.delete',
                'case.review.create','case.review.read','case.review.update',
            ]);

        // Community Provider
        Role::firstOrCreate(['name' => 'community_provider'])
            ->syncPermissions([
                'schedules.create','schedules.read','schedules.update','schedules.delete',
                'attendance.create','attendance.read','attendance.update','attendance.delete',
                'activities.sessions.create','activities.sessions.read',
                'activities.sessions.update','activities.sessions.delete',
                'activity.beneficiary.read_minimal',
            ]);

        // Donor
        Role::firstOrCreate(['name' => 'donor'])
            ->syncPermissions([
                'program.read.funded','program_funding.read.self','donor_report.read',
                'program.report.read.aggregated','program.analytics.read','region.statistics.read',
            ]);

        // Researcher
        Role::firstOrCreate(['name' => 'researcher'])
            ->syncPermissions([
                'program.read.all','program.analytics.read.full',
                'program.report.read.aggregated','program.report.read.comparative',
                'region.statistics.read',
            ]);

        // Program Manager
        Role::firstOrCreate(['name' => 'program_manager'])
            ->syncPermissions([
                'programs.create','programs.read','programs.update','programs.delete','programs.approve',
                'activities.create','activities.read','activities.update','activities.delete',
                'resources.allocate','resources.read','resources.update',
                'reports.read','statistics.read',
            ]);

        // Case Coordinator
        Role::firstOrCreate(['name' => 'case_coordinator'])
            ->syncPermissions([
                'file.read','file.update',
                'case.create','case.read','case.update',
                'case.support.plan.create','case.support.plan.read',
                'case.support.plan.update','case.support.plan.delete',
                'case.plan.goal.create','case.plan.goal.read',
                'case.plan.goal.update','case.plan.goal.delete',
                'case.event.create','case.event.read','case.event.update',
                'case.referral.create','case.referral.read','case.referral.update',
                'case.specialist.assign','case.specialist.revoke',
                'service.create','service.read','service.update','service.delete',
            ]);
    }
}
