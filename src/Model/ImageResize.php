<?php

namespace Keven1024\CSDN\Model;

use Flarum\Database\AbstractModel;

class ImageResize extends AbstractModel
{
    protected $table = 'keven1024_image_resize';
    protected $primaryKey = 'hash';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $attributes = [
        'original_img_width' => 0,
        'original_img_height' => 0,
        'original_img_size' => 0,
        'resize_img_filename' => null,
        'resize_img_ext' => null,
        'resize_img_width' => 0,
        'resize_img_height' => 0,
        'resize_img_size' => 0,
    ];

}
