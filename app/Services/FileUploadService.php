<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploadService
{
    private $fileInstance,
        $pathToSave,
        $oldFile,
        $uploadedFileName,
        $realPath;

    /**
     * __construct
     *
     * @param  mixed $fileInstance
     * @param  string $pathToSave
     * @param  string $oldFile
     * @return void
     */
    public function __construct($fileInstance, $pathToSave, $oldFile = null)
    {
        $this->fileInstance = $fileInstance;
        $this->pathToSave = $pathToSave;
        $this->oldFile = $oldFile;
        $this->uploadedFileName = null;
        $this->realPath = null;
    }

    /**
     * handle resize
     *
     * @param  int $xSize
     * @param  int $ySize
     * @return $this
     */
    public function resize(int $xSize, int $ySize)
    {
        $imageResize = Image::make($this->realPath);

        $imageResize->resize($xSize, $ySize)->save($this->realPath);

        return $this;
    }

    /**
     * handle file upload
     *
     * @return $this
     */
    public function upload(bool $deleteOldFile = false)
    {
        $originalName = pathinfo($this->fileInstance->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = pathinfo($this->fileInstance->getClientOriginalName(), PATHINFO_EXTENSION);

        if ($this->oldFile) {
            if (file_exists($this->oldFile)) {
                $timeStamps = str_replace(['-', ':', ' '], '', date('dmYHis')) . rand(0, 999);
                $originalName = $originalName . '-' . $timeStamps;

                if ($deleteOldFile) {
                    File::delete($this->oldFile);
                }
            }
        }

        $renamedFile = str_replace(' ', '-', $originalName) . '.' . $extension;

        $realPath = $this->fileInstance->storeAs(
            $this->pathToSave,
            $renamedFile,
            'public'
        );

        $this->uploadedFileName = $renamedFile;
        $this->realPath = 'storage/' . $realPath;

        return $this;
    }

    /**
     * return uploaded FileName
     *
     * @return string
     */
    public function getFileName()
    {
        return (string) $this->uploadedFileName;
    }
}
