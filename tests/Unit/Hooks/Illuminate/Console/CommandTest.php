<?php

declare(strict_types=1);

namespace OpenTelemetry\Tests\Contrib\Instrumentation\Laravel\Unit\Hooks\Illuminate\Console;

use OpenTelemetry\API\Instrumentation\AutoInstrumentation\Context;
use OpenTelemetry\API\Instrumentation\AutoInstrumentation\HookManagerInterface;
use OpenTelemetry\Contrib\Instrumentation\Laravel\Hooks\Illuminate\Console\Command;
use OpenTelemetry\Contrib\Instrumentation\Laravel\LaravelConfiguration;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    public function test_command_hook_can_be_disabled(): void
    {
        $hookManager = $this->createMock(HookManagerInterface::class);
        $hookManager->expects($this->never())->method('hook');

        (new Command())->instrument(
            new LaravelConfiguration(traceCommandEnabled: false),
            $hookManager,
            new Context(),
        );
    }

    public function test_command_hook_is_enabled_by_default(): void
    {
        $hookManager = $this->createMock(HookManagerInterface::class);
        $hookManager->expects($this->once())
            ->method('hook')
            ->with('Illuminate\\Console\\Command', 'execute');

        (new Command())->instrument(
            new LaravelConfiguration(),
            $hookManager,
            new Context(),
        );
    }
}
