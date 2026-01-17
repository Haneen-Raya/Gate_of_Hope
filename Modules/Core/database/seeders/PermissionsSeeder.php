<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Users & Roles
            'users.create','users.read','users.update','users.delete',
            'roles.create','roles.read','roles.update','roles.delete',
            'user_roles.assign','user_roles.revoke','user_roles.read',
            'permissions.create','permissions.read','permissions.update','permissions.delete',

            // System
            'system_settings.read','system_settings.update',
            'audit_logs.read',

            // Beneficiaries
            'beneficiaries.create','beneficiaries.read','beneficiaries.read_sensitive',
            'beneficiaries.update','beneficiaries.delete','beneficiaries.archive',

            'beneficiary_documents.create','beneficiary_documents.read',
            'beneficiary_documents.update','beneficiary_documents.delete',

            // Assessments
            'assessments.create','assessments.read','assessments.update','assessments.delete',

            // Cases
            'case_assignments.create','case_assignments.read',
            'case_assignments.update','case_assignments.delete',

            'cases.read','case_support_plans.read','case_sessions.read',
            'case_reviews.read','case_referrals.read',

            // Issues
            'issue_categories.create','issue_categories.read',
            'issue_categories.update','issue_categories.delete','issue_categories.archive',

            'issue_types.create','issue_types.read',
            'issue_types.update','issue_types.archive',

            // Professions
            'professions.create','professions.read',
            'professions.update','professions.delete',

            // Auth
            'auth.register','auth.login','auth.logout','auth.reset_password',

            // Beneficiary self
            'beneficiary.profile.read','beneficiary.profile.update',
            'beneficiary.documents.create','beneficiary.documents.read','beneficiary.documents.delete',

            'assessment.start','assessment.submit_external','assessment_result.read.self_summary',
            'sessions.read.self',

            'activity.consent.grant','activity.consent.revoke','activity.consent.read.self',
            'activity.read.self','activity.register.self','activity.unregister.self',

            'case.read.self','case_support_plan.read.self','case_plan_goal.read.self',
            'notifications.read.self','feedback.create.self','support_request.create.self',

            // Specialist
            'file.read','file.update',
            'sessions.create','sessions.read','sessions.update','sessions.delete',
            'case.review.create','case.review.read','case.review.update',

            // Community provider
            'schedules.create','schedules.read','schedules.update','schedules.delete',
            'attendance.create','attendance.read','attendance.update','attendance.delete',
            'activities.sessions.create','activities.sessions.read',
            'activities.sessions.update','activities.sessions.delete',
            'activity.beneficiary.read_minimal',

            // Donor & Research
            'program.read.funded','program_funding.read.self','donor_report.read',
            'program.report.read.aggregated','program.analytics.read','region.statistics.read',
            'program.read.all','program.analytics.read.full',
            'program.report.read.comparative',

            // Program manager
            'programs.create','programs.read','programs.update','programs.delete','programs.approve',
            'activities.create','activities.read','activities.update','activities.delete',
            'resources.allocate','resources.read','resources.update',
            'reports.read','statistics.read',

            // Case coordinator
            'case.create','case.read','case.update',
            'case.support.plan.create','case.support.plan.read',
            'case.support.plan.update','case.support.plan.delete',

            'case.plan.goal.create','case.plan.goal.read',
            'case.plan.goal.update','case.plan.goal.delete',

            'case.event.create','case.event.read','case.event.update',

            'case.referral.create','case.referral.read',
            'case.referral.update','case.specialist.assign','case.specialist.revoke',

            'service.create','service.read','service.update','service.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
