<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;

use Spatie\Image\Image as SpatieImage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\Image\Manipulations;  // This is required for FIT_CROP

use Intervention\Image\ImageManager;
// Use the driver you have installed, e.g., GdDriver
use Intervention\Image\Drivers\Gd\Driver;

class ApiController extends Controller
{
    public function checkApi(Request $request)
    {
        dd(1);
    }
    public function CompressVideo(Request $request)
    {

        $video = $request->file('video');
        $filename = time() . '.' . $video->getClientOriginalExtension();

        $highPath = public_path('uploads/videos/high/' . $filename);
        $video->move(public_path('uploads/videos/high/'), $filename);

        $lowPath = public_path('uploads/videos/low/' . $filename);

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => env('FFMPEG_BINARY_PATH', '/usr/bin/ffmpeg'),
            'ffprobe.binaries' => env('FFPROBE_BINARY_PATH', '/usr/bin/ffprobe'),
            'timeout'          => 3600,
        ]);

        $videoFFMpeg = $ffmpeg->open($highPath);

        // // ✅ Proper filter syntax (works with PHP-FFMpeg)
        // $videoFFMpeg->filters()
        //     ->resize(new Dimension(640, 360))
        //     ->synchronize(); // Keeps audio/video in sync

        $format = new X264('aac', 'libx264');
        $format->setKiloBitrate(500);
        $format->setAudioKiloBitrate(96);
        $format->setAdditionalParameters(['-crf', '30', '-preset', 'fast']);

        $videoFFMpeg->save($format, $lowPath);

        // ✅ Generate thumbnail
        $thumbnailName = time() . '.jpg';
        $thumbnailPath = public_path('uploads/videos/thumbnails/' . $thumbnailName);

        $videoFFMpeg->frame(TimeCode::fromSeconds(2))->save($thumbnailPath);

        return response()->json([
            'message' => 'Video uploaded successfully!',
            'high_quality_url' => asset('uploads/videos/high/' . $filename),
            'low_quality_url' => asset('uploads/videos/low/' . $filename),
            'thumbnail_url' => asset('uploads/videos/thumbnails/' . $thumbnailName),
        ]);
    }

    // public function CompressImage(Request $request)
    // {
    //     $imageName = time() . '.' . $request->image->extension();

    //     $baseDir = public_path('uploads/images/');

    //     // Ensure subfolders exist
    //     $highDir  = $baseDir . 'high/';
    //     $lowDir   = $baseDir . 'low/';
    //     $thumbDir = $baseDir . 'thumb/';
    //     foreach ([$highDir, $lowDir, $thumbDir] as $dir) {
    //         if (!file_exists($dir)) {
    //             mkdir($dir, 0755, true);
    //         }
    //     }
    //     // 1️⃣ Save high-quality image
    //     $highPath = $highDir . $imageName;
    //     $request->image->move($highDir, $imageName);

    //     // 2️⃣ Create low-quality resized image (width 800px)
    //     $lowPath = $lowDir . $imageName;
    //     SpatieImage::load($highPath)
    //         ->width(800)
    //         ->quality(60)
    //         ->save($lowPath);
    //     ImageOptimizer::optimize($lowPath);


    //     return response()->json([
    //         'message' => 'Image uploaded successfully!',
    //         'high_image' => $highPath,
    //         'low_image' =>   $lowPath,
    //     ]);
    // }

    public function CompressImage(Request $request)
    {
        // $request->validate([
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);

        $uploadedImage = $request->file('image');
        $imageName = time() . '.' . $uploadedImage->getClientOriginalExtension();
        $baseDir  = public_path('uploads/images/');
        $highDir  = $baseDir . 'high/';
        $lowDir   = $baseDir . 'low/';
        foreach ([$highDir, $lowDir] as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        $manager = new ImageManager(new Driver());
        // --- 1️⃣ HIGH QUALITY (Original) Save ---
        $highPath = $highDir . $imageName;

        $manager->read($uploadedImage->getRealPath())
            ->save($highPath, 90); // 90 is high quality for JPG

        // --- 2️⃣ LOW QUALITY (Watermarked, Reduced Size/Quality) Save ---
        $lowPath = $lowDir . 'low_' . $imageName;
        $watermarkPath = public_path('watermark.png');
        $lowQualityImage = $manager->read($uploadedImage->getRealPath());

        // B. **Watermark** (Requires your watermark image)
        if (file_exists($watermarkPath)) {
            $watermark = $manager->read($watermarkPath);
            $watermark->scale(width: $lowQualityImage->width() * 0.1);
            $lowQualityImage->place($watermark, 'bottom-right', 10, 10);
        }

        $lowQualityImage
            ->scale(width: 800)
            ->save($lowPath, 60); // 60 is the quality setting


        return response()->json([
            'message' => 'Image uploaded and processed successfully with Intervention Image!',
            'high_image' => $highPath,
            'low_image' => $lowPath,
        ]);
    }
}
