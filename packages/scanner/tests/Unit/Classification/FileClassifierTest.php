<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\FileType;
use CodeAtlas\Scanner\Classification\FileClassifier;

describe('FileClassifier — built-in prefix map', function (): void {
    $classifier = new FileClassifier();

    it('classifies controllers', function () use ($classifier): void {
        expect($classifier->classify('app/Http/Controllers/UserController.php'))->toBe(FileType::Controller);
    });

    it('classifies middleware', function () use ($classifier): void {
        expect($classifier->classify('app/Http/Middleware/Authenticate.php'))->toBe(FileType::Middleware);
    });

    it('classifies models', function () use ($classifier): void {
        expect($classifier->classify('app/Models/User.php'))->toBe(FileType::Model);
    });

    it('classifies services', function () use ($classifier): void {
        expect($classifier->classify('app/Services/UserService.php'))->toBe(FileType::Service);
    });

    it('classifies repositories', function () use ($classifier): void {
        expect($classifier->classify('app/Repositories/UserRepository.php'))->toBe(FileType::Repository);
    });

    it('classifies events, listeners, jobs, notifications', function () use ($classifier): void {
        expect($classifier->classify('app/Events/UserRegistered.php'))->toBe(FileType::Event);
        expect($classifier->classify('app/Listeners/SendWelcome.php'))->toBe(FileType::Listener);
        expect($classifier->classify('app/Jobs/SendEmail.php'))->toBe(FileType::Job);
        expect($classifier->classify('app/Notifications/InvoicePaid.php'))->toBe(FileType::Notification);
    });

    it('classifies policies, commands, providers', function () use ($classifier): void {
        expect($classifier->classify('app/Policies/UserPolicy.php'))->toBe(FileType::Policy);
        expect($classifier->classify('app/Console/Kernel.php'))->toBe(FileType::Command);
        expect($classifier->classify('app/Providers/AppServiceProvider.php'))->toBe(FileType::Provider);
    });

    it('classifies routes and config', function () use ($classifier): void {
        expect($classifier->classify('routes/web.php'))->toBe(FileType::Route);
        expect($classifier->classify('routes/api.php'))->toBe(FileType::Route);
        expect($classifier->classify('config/app.php'))->toBe(FileType::Config);
    });

    it('classifies migrations, factories, seeders', function () use ($classifier): void {
        expect($classifier->classify('database/migrations/2024_01_01_create_users_table.php'))->toBe(FileType::Migration);
        expect($classifier->classify('database/factories/UserFactory.php'))->toBe(FileType::Factory);
        expect($classifier->classify('database/seeders/UserSeeder.php'))->toBe(FileType::Seeder);
    });

    it('classifies blade views', function () use ($classifier): void {
        expect($classifier->classify('resources/views/welcome.blade.php'))->toBe(FileType::View);
    });

    it('returns Other for unknown paths', function () use ($classifier): void {
        expect($classifier->classify('random/thing.php'))->toBe(FileType::Other);
    });
});

describe('FileClassifier — normalization', function (): void {
    it('handles Windows-style backslashes', function (): void {
        expect((new FileClassifier())->classify('app\\Http\\Controllers\\X.php'))->toBe(FileType::Controller);
    });

    it('strips a leading slash', function (): void {
        expect((new FileClassifier())->classify('/app/Models/X.php'))->toBe(FileType::Model);
    });
});

describe('FileClassifier — custom overrides', function (): void {
    it('applies a custom prefix mapping', function (): void {
        $c = new FileClassifier(['custom/domain/' => FileType::Service]);
        expect($c->classify('custom/domain/Foo.php'))->toBe(FileType::Service);
    });

    it('falls through to built-in mappings when no override matches', function (): void {
        $c = new FileClassifier(['custom/domain/' => FileType::Service]);
        expect($c->classify('app/Models/X.php'))->toBe(FileType::Model);
    });
});
