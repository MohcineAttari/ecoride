<?php

declare(strict_types=1);

function load_env(): void
{
    $path = dirname(__DIR__, 2) . '/.env';
    if (!is_file($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

function env_value(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

function config(string $file): array
{
    return require dirname(__DIR__, 2) . '/config/' . $file . '.php';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);

    $viewPath = dirname(__DIR__) . '/View/' . $template . '.php';
    $layoutPath = dirname(__DIR__) . '/View/layout.php';

    require $layoutPath;
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_authenticated(): bool
{
    return current_user() !== null;
}
