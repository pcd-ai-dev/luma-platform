<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS IMAGE TOOLS
	//--------------------------------------------------------- 

        namespace Tools;

        require_once "ImageTools.interface.php";

        use GdImage;

        class ImageTools implements ImageToolsInterface {
            private ?GdImage $im = null;
            private ?GdImage $tmp_im = null;

            private string $image_type = '';
            private string $image_output_type = '';

            private string $watermark_font_path = '../src/fonts/ARIAL_0.TTF';

            /* ================= CONSTRUCTOR ================= */

            public function __construct(string $path_to_image)
            {
                if (!file_exists($path_to_image) || !is_file($path_to_image)) {
                    $this->parseError("Invalid Image Path :: $path_to_image");
                }

                ini_set('memory_limit', self::ALLOCATE_MEMORY);

                $extension = strtoupper(pathinfo($path_to_image, PATHINFO_EXTENSION));

                switch ($extension) {
                    case 'PNG':
                        $this->image_type = $this->image_output_type = 'PNG';
                        $this->im = imagecreatefrompng($path_to_image);
                        imagealphablending($this->im, false);
                        imagesavealpha($this->im, true);
                        break;

                    case 'JPG':
                    case 'JPEG':
                        $this->image_type = $this->image_output_type = 'JPG';
                        $this->im = imagecreatefromjpeg($path_to_image);
                        break;

                    case 'GIF':
                        $this->image_type = $this->image_output_type = 'GIF';
                        $this->im = imagecreatefromgif($path_to_image);
                        imagealphablending($this->im, false);
                        imagesavealpha($this->im, true);
                        break;

                    default:
                        $this->parseError("Unsupported image type ($extension)");
                }
            }

            /* ================= BASIC ================= */

            public function getX(): int
            {
                return imagesx($this->im);
            }

            public function getY(): int
            {
                return imagesy($this->im);
            }

            private function getColor(string $hex): int
            {
                [$r, $g, $b] = $this->hexToRGB($hex);
                return imagecolorallocate($this->im, $r, $g, $b);
            }

            public function hexToRGB(string $hex): array
            {
                $hex = ltrim($hex, '#');

                if (!preg_match('/^[A-Fa-f0-9]{3}$|^[A-Fa-f0-9]{6}$/', $hex)) {
                    return [255, 255, 255];
                }

                return $this->html2rgb($hex);
            }

            private function html2rgb(string $color): array
            {
                if (strlen($color) === 6) {
                    return [
                        hexdec(substr($color, 0, 2)),
                        hexdec(substr($color, 2, 2)),
                        hexdec(substr($color, 4, 2))
                    ];
                }

                return [
                    hexdec($color[0] . $color[0]),
                    hexdec($color[1] . $color[1]),
                    hexdec($color[2] . $color[2])
                ];
            }

            /* ================= EFFECTS ================= */

            public function reflect(int $percent = 35, string $bg_color = "#FFF", int $spacing = -1): void
            {
                $percent = max(0, min(100, $percent));
                if ($percent === 0) return;

                $ref_h = (int) ceil(($percent / 100) * $this->getY());
                $total_height = $this->getY() + $ref_h;

                $this->tmp_im = imagecreatetruecolor($this->getX(), $total_height);
                imagefill($this->tmp_im, 0, 0, $this->getColor($bg_color));
                imagecopy($this->tmp_im, $this->im, 0, 0, 0, 0, $this->getX(), $this->getY());

                $alpha = 100;
                $step = 100 / $ref_h;

                for ($i = 0; $i < $ref_h; $i++) {
                    imagecopymerge(
                        $this->tmp_im,
                        $this->im,
                        0,
                        $this->getY() + $i + $spacing,
                        0,
                        $this->getY() - $i - 1,
                        $this->getX(),
                        1,
                        (int)$alpha
                    );
                    $alpha -= $step;
                }

                $this->im = $this->tmp_im;
            }

            public function setBrightness(int $brightness = 0): void
            {
                imagefilter($this->im, IMG_FILTER_BRIGHTNESS, $brightness);
            }

            public function setContrast(int $contrast = 0): void
            {
                imagefilter($this->im, IMG_FILTER_CONTRAST, $contrast);
            }

            public function grayscaleImage(): void
            {
                imagefilter($this->im, IMG_FILTER_GRAYSCALE);
            }

            public function addBlur(bool $gausian = false): void
            {
                imagefilter(
                    $this->im,
                    $gausian ? IMG_FILTER_GAUSSIAN_BLUR : IMG_FILTER_SELECTIVE_BLUR
                );
            }

            public function addGaussianBlur(): void
            {
                $this->addBlur(true);
            }

            /* ================= RESIZE ================= */

            public function resizeOriginal(int $new_width, int $new_height): void
            {
                $this->tmp_im = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled(
                    $this->tmp_im,
                    $this->im,
                    0, 0, 0, 0,
                    $new_width, $new_height,
                    $this->getX(), $this->getY()
                );
                $this->im = $this->tmp_im;
            }

            public function resizeWidth(int $new_width): void
            {
                $h = (int) floor($new_width * ($this->getY() / $this->getX()));
                $this->resizeOriginal($new_width, $h);
            }

            public function resizeHeight(int $new_height): void
            {
                $w = (int) floor($new_height * ($this->getX() / $this->getY()));
                $this->resizeOriginal($w, $new_height);
            }

            public function resizeNewByWidth(int $width, int $height, int $resize_width, string $bgcolor = "#FFF"): void
            {
                $bg = $this->getColor($bgcolor);
                $canvas = imagecreatetruecolor($width, $height);
                imagefill($canvas, 0, 0, $bg);

                $this->resizeWidth($resize_width);

                imagecopy(
                    $canvas,
                    $this->im,
                    (int)(($width - $this->getX()) / 2),
                    (int)(($height - $this->getY()) / 2),
                    0, 0,
                    $this->getX(),
                    $this->getY()
                );

                $this->im = $canvas;
            }

            public function resizeNewByHeight(int $width, int $height, int $resize_height, string $bgcolor = "#FFF"): void
            {
                $bg = $this->getColor($bgcolor);
                $canvas = imagecreatetruecolor($width, $height);
                imagefill($canvas, 0, 0, $bg);

                $this->resizeHeight($resize_height);

                imagecopy(
                    $canvas,
                    $this->im,
                    (int)(($width - $this->getX()) / 2),
                    (int)(($height - $this->getY()) / 2),
                    0, 0,
                    $this->getX(),
                    $this->getY()
                );

                $this->im = $canvas;
            }

            /* ================= WATERMARK ================= */

            public function addWatermark(
                string $text,
                int $vertical_position,
                int $horizontal_position,
                int $font_size = 12,
                string $fontcolor = "#FFF",
                int $angle = 0,
                int $margin = 5
            ): void {
                $box = imagettfbbox($font_size, $angle, $this->watermark_font_path, $text);
                $w = abs($box[2] - $box[0]);
                $h = abs($box[7] - $box[1]);

                $x = match ($horizontal_position) {
                    self::IMAGE_POSITION_LEFT => $margin,
                    self::IMAGE_POSITION_RIGHT => $this->getX() - $w - $margin,
                    default => (int)(($this->getX() - $w) / 2)
                };

                $y = match ($vertical_position) {
                    self::IMAGE_POSITION_TOP => $h + $margin,
                    self::IMAGE_POSITION_BOTTOM => $this->getY() - $margin,
                    default => (int)(($this->getY() + $h) / 2)
                };

                imagettftext(
                    $this->im,
                    $font_size,
                    $angle,
                    $x,
                    $y,
                    $this->getColor($fontcolor),
                    $this->watermark_font_path,
                    $text
                );
            }

            public function addWatermarkImage(string $image_path, int $vertical_position, int $horizontal_position, int $margin = 5): void
            {
                $wm = new self($image_path);

                $x = match ($horizontal_position) {
                    self::IMAGE_POSITION_LEFT => $margin,
                    self::IMAGE_POSITION_RIGHT => $this->getX() - $wm->getX() - $margin,
                    default => (int)(($this->getX() - $wm->getX()) / 2)
                };

                $y = match ($vertical_position) {
                    self::IMAGE_POSITION_TOP => $margin,
                    self::IMAGE_POSITION_BOTTOM => $this->getY() - $wm->getY() - $margin,
                    default => (int)(($this->getY() - $wm->getY()) / 2)
                };

                imagecopy($this->im, $wm->im, $x, $y, 0, 0, $wm->getX(), $wm->getY());
            }

            /* ================= OTHER ================= */

            public function setTransparentBg(): void
            {
                $c = imagecolorallocate($this->im, 0, 0, 0);
                imagecolortransparent($this->im, $c);
            }

            public function setOutputType(int $image_type): void
            {
                $this->image_output_type = match ($image_type) {
                    self::IMAGE_TYPE_PNG => 'PNG',
                    self::IMAGE_TYPE_GIF => 'GIF',
                    default => 'JPG'
                };
            }

            public function cropImage(int $x, int $y, int $width, int $height): void
            {
                $this->tmp_im = imagecreatetruecolor($width, $height);
                imagecopy($this->tmp_im, $this->im, 0, 0, $x, $y, $width, $height);
                $this->im = $this->tmp_im;
            }

            public function rotateImage(int $degree, ?string $bg = null): void
            {
                $this->im = imagerotate(
                    $this->im,
                    -($degree % 360),
                    $this->getColor($bg ?? "#FFF")
                );
            }

            public function rotateLeft(): void
            {
                $this->rotateImage(90);
            }

            public function rotateRight(): void
            {
                $this->rotateImage(270);
            }

            public function showImage(): void
            {
                if (!headers_sent()) {
                    header(match ($this->image_output_type) {
                        'PNG' => 'Content-Type: image/png',
                        'GIF' => 'Content-Type: image/gif',
                        default => 'Content-Type: image/jpeg'
                    });
                }

                match ($this->image_output_type) {
                    'PNG' => imagepng($this->im),
                    'GIF' => imagegif($this->im),
                    default => imagejpeg($this->im)
                };
            }

            public function save(string $path, string $name, int $quality = 90, bool $overwrite = true): void
            {
                $file = $path . $name;
                if (!$overwrite && file_exists($file)) {
                    $this->parseError("File exists");
                }

                match (strtoupper(pathinfo($name, PATHINFO_EXTENSION))) {
                    'PNG' => imagepng($this->im, $file),
                    'GIF' => imagegif($this->im, $file),
                    default => imagejpeg($this->im, $file, 100)
                };
            }

            public function parseError(string $msg): void
            {
                $img = imagecreatetruecolor(520, 60);
                $white = imagecolorallocate($img, 255, 255, 255);
                imagestring($img, 4, 5, 20, strip_tags($msg), $white);

                if (!headers_sent()) {
                    header("Content-Type: image/jpeg");
                }

                imagejpeg($img);
                imagedestroy($img);
                exit;
            }

            public function destroy(): void{
                if ($this->im instanceof GdImage) {
                    imagedestroy($this->im);
                    $this->im = null;
                }

                if ($this->tmp_im instanceof GdImage) {
                    imagedestroy($this->tmp_im);
                    $this->tmp_im = null;
                }
            }

        }