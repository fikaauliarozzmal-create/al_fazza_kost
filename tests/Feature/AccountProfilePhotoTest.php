<?php

namespace Tests\Feature;

use App\Http\Controllers\AccountProfileController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class AccountProfilePhotoTest extends TestCase
{
    public function test_admin_photo_is_stored_for_the_authenticated_admin_only(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile-photos/admin-lama.jpg', 'old');
        $user = $this->user(['role' => 'Admin', 'profile_photo_path' => 'profile-photos/admin-lama.jpg']);

        $request = $this->photoRequest($user);
        DB::shouldReceive('transaction')->once()->andReturnUsing(fn (callable $callback) => $callback());

        $response = app(AccountProfileController::class)->updateAdminPhoto($request);

        $this->assertSame(route('admin.profil'), $response->getTargetUrl());
        $this->assertStringStartsWith('profile-photos/', $user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
        Storage::disk('public')->assertMissing('profile-photos/admin-lama.jpg');
    }

    public function test_active_resident_photo_uses_its_own_authenticated_account(): void
    {
        Storage::fake('public');
        $user = $this->user(['role' => 'User', 'status_akun' => 'Aktif']);
        $user->shouldReceive('isPenghuniAktif')->once()->andReturnTrue();

        $request = $this->photoRequest($user);
        DB::shouldReceive('transaction')->once()->andReturnUsing(fn (callable $callback) => $callback());

        $response = app(AccountProfileController::class)->updatePenghuniPhoto($request);

        $this->assertSame(route('penghuni.profil'), $response->getTargetUrl());
        $this->assertStringStartsWith('profile-photos/', $user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_initials_are_used_when_no_profile_photo_exists(): void
    {
        $user = new User(['name' => 'Khalifatun Khasanah']);

        $this->assertSame('KK', $user->initials());
        $this->assertNull($user->profilePhotoUrl());
    }

    public function test_profile_photo_url_uses_the_active_application_base_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile-photos/foto saya.jpg', 'image');
        $user = new User(['profile_photo_path' => 'profile-photos/foto saya.jpg']);
        $request = Request::create(
            'http://localhost/al_fazza_kost/public/penghuni/profil',
            'GET',
            [],
            [],
            [],
            [
                'SCRIPT_NAME' => '/al_fazza_kost/public/index.php',
                'SCRIPT_FILENAME' => public_path('index.php'),
                'PHP_SELF' => '/al_fazza_kost/public/index.php/penghuni/profil',
            ]
        );
        app()->instance('request', $request);

        $this->assertSame(
            '/al_fazza_kost/public/storage/profile-photos/foto%20saya.jpg',
            $user->profilePhotoUrl()
        );
    }

    public function test_invalid_profile_photo_is_rejected_before_any_file_or_user_change(): void
    {
        Storage::fake('public');
        $user = new User(['name' => 'Admin', 'role' => 'Admin', 'profile_photo_path' => 'profile-photos/lama.jpg']);
        $request = Request::create('/admin/profil/foto', 'PUT', [], [], [
            'profile_photo' => UploadedFile::fake()->create('bukan-gambar.txt', 12, 'text/plain'),
        ]);
        $request->setUserResolver(fn () => $user);

        try {
            app(AccountProfileController::class)->updateAdminPhoto($request);
            $this->fail('File non-gambar seharusnya ditolak oleh validasi.');
        } catch (ValidationException) {
            // Expected: validasi berhenti sebelum proses simpan dijalankan.
        }

        $this->assertSame('profile-photos/lama.jpg', $user->profile_photo_path);
        Storage::disk('public')->assertDirectoryEmpty('profile-photos');
    }

    private function user(array $attributes): User
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->forceFill(array_merge(['name' => 'Admin Al Fazza', 'status_akun' => 'Aktif'], $attributes));
        $user->shouldReceive('save')->once()->andReturnTrue();

        return $user;
    }

    private function photoRequest(User $user): Request
    {
        $request = Request::create('/profil/foto', 'PUT', [], [], [
            'profile_photo' => new UploadedFile(
                public_path('images/logo al fazza.jpg'),
                'foto-profil.jpg',
                'image/jpeg',
                null,
                true
            ),
        ]);
        $request->setUserResolver(fn () => $user);

        return $request;
    }
}
