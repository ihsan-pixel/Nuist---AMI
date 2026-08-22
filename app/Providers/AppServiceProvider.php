<?php

namespace App\Providers;

use App\Models\School;
use App\Models\AmiSchoolAssignment;
use App\Models\AmiAssessment;
use App\Models\AmiFinding;
use App\Models\AmiFollowUp;
use App\Policies\AuditorAssessmentPolicy;
use App\Policies\AuditorAssignmentPolicy;
use App\Policies\AuditorFindingPolicy;
use App\Policies\AmiFollowUpPolicy;
use App\Policies\SchoolPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(School::class, SchoolPolicy::class);
        Gate::policy(AmiSchoolAssignment::class, AuditorAssignmentPolicy::class);
        Gate::policy(AmiAssessment::class, AuditorAssessmentPolicy::class);
        Gate::policy(AmiFinding::class, AuditorFindingPolicy::class);
        Gate::policy(AmiFollowUp::class, AmiFollowUpPolicy::class);
    }
}
