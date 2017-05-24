<?php

namespace App;

use Image;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Photo extends Model {

    /**
     * Fillable fields for a photo
     *
     * @var array
     */
    protected $fillable = ['path', 'name', 'thumbnail_path'];

    protected $appends = ['url', 'thumbnail_url'];

    /**
     * The UploadedFile instance
     *
     * @var UploadedFile
     */
    protected $file;

    /**
     * When a photo is created, prepare a thumbnail
     *
     * @return void
     */
    protected static function boot() {
        static::creating(function($photo) {
            return $photo->upload();
        });
    }

    /**
     * Make a new photo instance from an uploaded file
     *
     * @var $file
     * @return self
     */
    public static function fromForm($file) {
        $photo = new static;

        $photo->file = $file;
        $photo->name = $photo->fileName();

        return $photo->fill([
            'path' => $photo->filePath(),
            'thumbnail_path' => $photo->thumbnailPath()
        ]);
    }

    /**
     * Get the file name for the photo
     *
     * @return string
     */
    public function fileName() {
        if(!is_null($this->name) && $this->name)
            return $this->name;

        $name = sha1(
            $this->file->getClientOriginalName() . '-' . microtime()
        );

        $extension = $this->file->getClientOriginalExtension();

        return "{$name}.{$extension}";
    }

    /**
     * Get the path to the photo
     *
     * @return string
     */
    public function filePath() {
        return $this->baseDir() . '/' . $this->fileName();
    }

    /**
     * Get the base directory for photo uploads
     *
     * @return string
     */
    public function baseDir() {
        return 'images/photos';
    }

    /**
     * Get the path to the photo's thumbnail
     *
     * @return string
     */
    public function thumbnailPath() {
        return $this->baseDir() . '/tn-' . $this->fileName();
    }

    /**
     * Move the photo to the proper folder
     *
     * @return self
     */
    public function upload() {
        $this->file->move($this->baseDir(), $this->fileName());

        $this->makeThumbnail();

        return $this;
    }

    /**
     * Create a thumbnail for the photo
     *
     * @return void
     */
    protected function makeThumbnail() {
        Image::make($this->filePath())
            ->fit(200, 200, function($constraint) {
                $constraint->upsize();
            }, 'center')
            ->save($this->thumbnailPath());
    }

    public function delete() {
        \File::delete([
            $this->path,
            $this->thumbnail_path
        ]);

        parent::delete();
    }

    public function getUrlAttribute() {
        return \URL::to($this->path);
    }

    public function getThumbnailUrlAttribute() {
        return \URL::to($this->thumbnail_path);
    }
}
