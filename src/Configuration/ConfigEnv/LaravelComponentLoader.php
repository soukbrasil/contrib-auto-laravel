<?php

declare(strict_types=1);

namespace OpenTelemetry\Contrib\Instrumentation\Laravel\Configuration\ConfigEnv;

use function in_array;
use OpenTelemetry\API\Configuration\ConfigEnv\EnvComponentLoader;
use OpenTelemetry\API\Configuration\ConfigEnv\EnvComponentLoaderRegistry;
use OpenTelemetry\API\Configuration\ConfigEnv\EnvResolver;
use OpenTelemetry\API\Configuration\Context;
use OpenTelemetry\API\Instrumentation\AutoInstrumentation\InstrumentationConfiguration;
use OpenTelemetry\Contrib\Instrumentation\Laravel\LaravelConfiguration;

/**
 * @implements EnvComponentLoader<InstrumentationConfiguration>
 * @psalm-suppress UnusedClass
 */
final class LaravelComponentLoader implements EnvComponentLoader
{
    #[\Override]
    public function load(EnvResolver $env, EnvComponentLoaderRegistry $registry, Context $context): InstrumentationConfiguration
    {
        $disabledInstrumentations = $env->list('OTEL_PHP_DISABLED_INSTRUMENTATIONS');
        $isInstrumentationEnabled = static function (string $name) use ($disabledInstrumentations): bool {
            return !$disabledInstrumentations
                || !in_array('all', $disabledInstrumentations, true)
                && !in_array($name, $disabledInstrumentations, true);
        };

        return new LaravelConfiguration(
            enabled: $isInstrumentationEnabled('laravel'),
            traceCliEnabled: $env->bool('OTEL_PHP_TRACE_CLI_ENABLED') ?? false,
            traceCommandEnabled: $isInstrumentationEnabled('laravel-command'),
        );
    }

    #[\Override]
    public function name(): string
    {
        return LaravelConfiguration::class;
    }
}
