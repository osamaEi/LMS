<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CompressImages extends Command
{
    protected $signature = 'images:compress {--dir=lms3 : Subdirectory under public/}';
    protected $description = 'Compress PNG/JPEG images in public/ to reduce file size';

    public function handle(): int
    {
        $dir = public_path($this->option('dir'));

        if (! is_dir($dir)) {
            $this->error("Directory not found: $dir");
            return 1;
        }

        $files = File::allFiles($dir);
        $images = array_filter($files, fn($f) => in_array(
            strtolower($f->getExtension()), ['png', 'jpg', 'jpeg']
        ));
        $images = array_values($images);

        if (empty($images)) {
            $this->info('No images found.');
            return 0;
        }

        $bar = $this->output->createProgressBar(count($images));
        $bar->start();

        $saved = 0;
        $errors = 0;

        foreach ($images as $file) {
            $path = $file->getPathname();
            $ext  = strtolower($file->getExtension());
            $before = filesize($path);

            try {
                $src = $ext === 'png'
                    ? @imagecreatefrompng($path)
                    : @imagecreatefromjpeg($path);

                if (! $src) {
                    $errors++;
                    $bar->advance();
                    continue;
                }

                $ow = imagesx($src);
                $oh = imagesy($src);
                $nw = (int) round($ow * 0.70);
                $nh = (int) round($oh * 0.70);

                $dst = imagecreatetruecolor($nw, $nh);

                if ($ext === 'png') {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                    imagefilledrectangle($dst, 0, 0, $nw, $nh, $transparent);
                }

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $ow, $oh);

                if ($ext === 'png') {
                    imagepng($dst, $path, 7);
                } else {
                    imagejpeg($dst, $path, 70);
                }

                clearstatcache(true, $path);
                $after = filesize($path);
                $saved += max(0, $before - $after);
            } catch (\Throwable) {
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info(sprintf(
            'Done. Processed %d images. Saved %s KB. Errors: %d.',
            count($images),
            number_format($saved / 1024, 1),
            $errors
        ));

        return 0;
    }
}
