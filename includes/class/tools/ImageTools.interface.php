<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS IMAGE TOOLS INTERFACE
	//--------------------------------------------------------- 

        namespace Tools;

        interface ImageToolsInterface{
            
            /* Memory size to allocate per image */
            public const ALLOCATE_MEMORY = '50M';

            /* Image Types */
            public const IMAGE_TYPE_JPG  = 1;
            public const IMAGE_TYPE_JPEG = 1;
            public const IMAGE_TYPE_PNG  = 2;
            public const IMAGE_TYPE_GIF  = 3;

            /* Positions */
            public const IMAGE_POSITION_TOP    = 1;
            public const IMAGE_POSITION_CENTER = 2;
            public const IMAGE_POSITION_BOTTOM = 3;

            public const IMAGE_POSITION_LEFT   = 4;
            public const IMAGE_POSITION_RIGHT  = 5;

            /* Effects */

            public function reflect(
                int $percent = 35,
                string $bg_color = "#FFF",
                int $spacing = -1
            ): void;

            public function setBrightness(int $brightness = 0): void;

            public function setContrast(int $contrast = 0): void;

            public function grayscaleImage(): void;

            public function addBlur(bool $gausian = false): void;

            public function addGaussianBlur(): void;

            /* Resizing */

            public function resizeOriginal(int $new_width, int $new_height): void;

            public function resizeWidth(int $new_width): void;

            public function resizeHeight(int $new_height): void;

            public function resizeNewByWidth(
                int $width,
                int $height,
                int $resize_width,
                string $bgcolor = "#FFF"
            ): void;

            public function resizeNewByHeight(
                int $width,
                int $height,
                int $resize_height,
                string $bgcolor = "#FFF"
            ): void;

            /* Watermarking */

            public function addWatermark(
                string $text,
                int $vertical_position,
                int $horizontal_position,
                int $font_size = 12,
                string $fontcolor = "#FFF",
                int $angle = 0,
                int $margin = 5
            ): void;

            public function addWatermarkImage(
                string $image_path,
                int $vertical_position,
                int $horizontal_position,
                int $margin = 5
            ): void;

            /* Cropping */

            public function cropImage(
                int $x,
                int $y,
                int $width,
                int $height
            ): void;

            /* Rotation */

            public function rotateLeft(): void;

            public function rotateRight(): void;

            public function rotateImage(int $degree, ?string $bg = null): void;

            /* Other */

            public function setTransparentBg(): void;

            public function hexToRGB(string $hex): array;

            /* Output */

            public function setOutputType(int $image_type): void;

            public function save(
                string $path,
                string $name,
                int $quality = 90,
                bool $overwrite = true
            ): void;

            public function showImage(): void;
        }