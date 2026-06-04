<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS COMMON INTERFACE IMAGE FORM MANAGER
	//--------------------------------------------------------- 


    namespace Tools;

    interface FileOwnerInterface {

        public function getFile(int $id, string $field): ?string;
        public function updateFile(int $id, string $field, string $target, string $filename): void;
        public function resetFile(int $id, string $field, string $target): void;
        public function getFilePath(int $idCategory, string $folder): string;

        public function getGalleryImage(int $id): ?string;
        public function insertImage(int $id, int $idCategory, string $filename): void;
        public function updateImage(int $id, string $field, string $target, string $filename): void;
        public function deleteImage(int $id): void;
        
        public function getFolderPath(int $idCategory, string $folder): string;
        public function getImagePath(int $idCategory, string $folder): string;
        public function getRecPath(int $idCategory, string $folder): string;
        public function getSquarePath(int $idCategory, string $folder): string;
        public function getMobilePath(int $idCategory, string $folder): string;
        public function imgTreatment(string $filename, int $idCategory, string $field, string $folder):void;

        public function insertDocument(int $id, int $idParent, string $filename): void;
        public function deleteDocument(int $id, int $idSite): void;
        
    }