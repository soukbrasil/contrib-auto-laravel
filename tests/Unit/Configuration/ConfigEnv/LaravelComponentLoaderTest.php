<?php

declare(strict_types=1);

namespace OpenTelemetry\Tests\Contrib\Instrumentation\Laravel\Unit\Configuration\ConfigEnv;

use OpenTelemetry\API\Configuration\ConfigEnv\EnvComponentLoaderRegistry;
use OpenTelemetry\API\Configuration\ConfigEnv\EnvResolver;
use OpenTelemetry\API\Configuration\Context;
use OpenTelemetry\Contrib\Instrumentation\Laravel\Configuration\ConfigEnv\LaravelComponentLoader;
use OpenTelemetry\Contrib\Instrumentation\Laravel\LaravelConfiguration;
use PHPUnit\Framework\TestCase;

final class LaravelComponentLoaderTest extends TestCase
{
    public function test_laravel_command_can_be_disabled_independently(): void
    {
        $env = $this->createMock(EnvResolver::class);
        $env->expects($this->once())
            ->method('list')
            ->with('OTEL_PHP_DISABLED_INSTRUMENTATIONS')
            ->willReturn(['laravel-command']);
        $env->method('bool')->with('OTEL_PHP_TRACE_CLI_ENABLED')->willReturn(null);

        $configuration = (new LaravelComponentLoader())->load(
            $env,
            $this->createMock(EnvComponentLoaderRegistry::class),
            new Context(),
        );

        self::assertInstanceOf(LaravelConfiguration::class, $configuration);
        self::assertTrue($configuration->enabled);
        self::assertFalse($configuration->traceCommandEnabled);
    }
}
