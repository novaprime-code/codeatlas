<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Exceptions;

final class ContainerException extends CodeAtlasException
{
    public static function notBound(string $abstract): self
    {
        return new self("No binding registered for: {$abstract}");
    }

    public static function circularDependency(string $abstract): self
    {
        return new self("Circular dependency detected while resolving: {$abstract}");
    }

    public static function unresolvableParameter(string $class, string $parameter): self
    {
        return new self("Cannot resolve parameter '{$parameter}' of class '{$class}'.");
    }
}
