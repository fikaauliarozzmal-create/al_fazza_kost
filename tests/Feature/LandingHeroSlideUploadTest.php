<?php

namespace Tests\Feature;

use App\Http\Controllers\LandingHeroSlideController;
use App\Models\LandingHeroSlide;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class LandingHeroSlideUploadTest extends TestCase
{
    public function test_uploaded_photo_is_stored_for_the_targeted_kost_and_redirects_to_settings(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('landing/hero/kost1-lama.jpg', 'old');

        $slide = Mockery::mock(LandingHeroSlide::class)->makePartial();
        $slide->forceFill([
            'judul' => 'Al Fazza Kost 1',
            'subjudul' => 'Karangmanyar, Purbalingga',
            'lokasi' => 'kost1',
            'foto' => 'landing/hero/kost1-lama.jpg',
            'aktif' => true,
        ]);
        $slide->shouldReceive('save')->once()->andReturnTrue();

        DB::shouldReceive('transaction')->once()->andReturnUsing(
            fn (callable $callback) => $callback()
        );

        $request = Request::create('/landing-hero/1', 'PUT', [
            'judul' => 'Al Fazza Kost 1',
            'subjudul' => 'Karangmanyar, Purbalingga',
            'aktif' => '1',
            'from_website_settings' => '1',
        ], [], [
            'foto' => new UploadedFile(
                public_path('images/logo al fazza.jpg'),
                'kost1-baru.jpg',
                'image/jpeg',
                null,
                true
            ),
        ]);

        $response = app(LandingHeroSlideController::class)->update($request, $slide);

        $this->assertSame(route('pengaturan-website.index'), $response->getTargetUrl());
        $this->assertSame('kost1', $slide->lokasi);
        $this->assertNotSame('landing/hero/kost1-lama.jpg', $slide->foto);
        Storage::disk('public')->assertExists($slide->foto);
        Storage::disk('public')->assertMissing('landing/hero/kost1-lama.jpg');
    }
}
