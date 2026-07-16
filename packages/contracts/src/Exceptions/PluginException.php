<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Exceptions;

final class PluginException extends CodeAtlasException
{
    public static function classNotFound(string $class): self
    {
        return new self("Plugin class does not exist: {$class}");
    }

    public static function doesNotImplementInterface(string $class): self
    {
        return new self("Plugin class does not implement PluginInterface: {$class}");
    }

    public static function registrationFailed(string $class, string $reason): self
    {
        return new self("Plugin '{$class}' failed to register: {$reason}");
    }
}
