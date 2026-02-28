<?php

namespace App\Providers;

use App\Fhir\Transformers\BundleTransformer;
use App\Fhir\Transformers\CapabilityStatementBuilder;
use App\Fhir\Transformers\HealthcareServiceTransformer;
use App\Fhir\Transformers\LocationTransformer;
use App\Fhir\Transformers\OrganizationTransformer;
use App\Fhir\Validation\FhirValidator;
use App\Models\FhirCodeMapping;
use Illuminate\Support\ServiceProvider;

/**
 * FHIR Service Provider
 *
 * Registers FHIR transformer classes and services in the container.
 * Warms the code mapping cache on boot.
 */
class FhirServiceProvider extends ServiceProvider
{
    /**
     * Register FHIR services in the container.
     */
    public function register(): void
    {
        // Register transformers as singletons (they are stateless)
        $this->app->singleton(OrganizationTransformer::class);
        $this->app->singleton(LocationTransformer::class);
        $this->app->singleton(HealthcareServiceTransformer::class);
        $this->app->singleton(BundleTransformer::class);
        $this->app->singleton(CapabilityStatementBuilder::class);

        // Register the FHIR validator
        $this->app->singleton(FhirValidator::class, function ($app) {
            return new FhirValidator(config('hfr.fhir.validator_url'));
        });
    }

    /**
     * Bootstrap FHIR services.
     */
    public function boot(): void
    {
        // Only warm cache if FHIR is enabled
        if (config('hfr.fhir.enabled', true)) {
            // Pre-warm code mappings cache on first request
            // (deferred to avoid slowing down artisan commands that don't need it)
            $this->app->booted(function () {
                try {
                    FhirCodeMapping::getCachedMappings();
                } catch (\Throwable $e) {
                    // Silently ignore — table may not exist yet (pre-migration)
                }
            });
        }
    }
}
