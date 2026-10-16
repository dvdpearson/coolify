<?php

use App\Livewire\Storage\Create;
use App\Livewire\Storage\Form;
use App\Models\InstanceSettings;
use App\Models\S3Storage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    InstanceSettings::unguarded(fn () => InstanceSettings::query()->create(['id' => 0]));

    $this->user = User::factory()->create();
    $this->team = Team::factory()->create();
    $this->user->teams()->attach($this->team, ['role' => 'owner']);

    $this->storage = S3Storage::create([
        'name' => 'Backblaze',
        'region' => 'us-east-005',
        'key' => 'key',
        'secret' => 'secret',
        'bucket' => 'bucket',
        'endpoint' => 'https://s3.us-east-005.backblazeb2.com',
        'team_id' => $this->team->id,
        'is_usable' => true,
    ]);

    $this->actingAs($this->user);
    session(['currentTeam' => $this->team]);
});

it('defaults existing storages to S3v4', function () {
    expect($this->storage->fresh()->signature_version)->toBe('S3v4');

    Livewire::test(Form::class, ['storage' => $this->storage])
        ->assertSet('signatureVersion', 'S3v4');
});

it('saves the selected signature version', function () {
    if (! defined('CURLOPT_RESOLVE')) {
        define('CURLOPT_RESOLVE', 10203);
    }
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('files')->andReturn([]);
    Storage::shouldReceive('build')->andReturn($disk);

    Livewire::test(Form::class, ['storage' => $this->storage])
        ->set('signatureVersion', 'S3v2')
        ->call('submit')
        ->assertHasNoErrors();

    expect($this->storage->fresh()->signature_version)->toBe('S3v2');
});

it('rejects an unknown signature version', function () {
    Livewire::test(Form::class, ['storage' => $this->storage])
        ->set('signatureVersion', 'S3v3')
        ->call('submit')
        ->assertDispatched('error');

    expect($this->storage->fresh()->signature_version)->toBe('S3v4');
});

it('shows the signature version on the create form with S3v4 selected', function () {
    Livewire::test(Create::class)
        ->assertSet('signatureVersion', 'S3v4')
        ->assertSee('Signature version');
});
