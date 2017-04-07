<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    return FALSE;
} else {
    list($files, $news_id) = $supplied_data;
}

$mime_types = array(
    "image/jpg",
    "image/jpeg",
    "image/png"
);

$extensions = array(
    "jpg",
    "jpeg",
    "png"
);

$directory = BASEPATH."[".$C_S::SUBSYSTEM_alpha."]/news";

if( !array_filter($files['name']) )
    return FALSE;

foreach($files['name'] as $key => $value)
{
    $fileinfo = new finfo(FILEINFO_MIME_TYPE);

    $mime = $fileinfo->file($files['tmp_name'][$key]);
    if( !in_array($mime, $mime_types, TRUE) ) {
        return FALSE;
    }

    $input_extension_array = explode(".", $files['name'][$key]);
    if( !in_array(end($input_extension_array), $extensions, TRUE) ) {
        return FALSE;
    }

    $filename = [
        'image'       => $news_id.".".end($input_extension_array),
        'image_thumb' => $news_id."_thumb.".end($input_extension_array)
    ];

    $file       = "{$directory}/{$news_id}." . end($input_extension_array);
    $file_thumb = "{$directory}/{$news_id}_thumb." . end($input_extension_array);

    $size = $files['size'][$key];
    $max_size = 10 * 1024 * 1024;

    // Native errors check
    if( $files['error'][$key] > 0 ) {
        continue;
    }

    // File size, enough folder space
    if( ($size > $max_size) || ((disk_free_space($directory) - $size) < $size) ) {
        continue;
    }

    #RESIZE
    list($width, $height) = getimagesize($files['tmp_name'][$key]);

    // check if the file is really an image
    if ($width == null && $height == null) {
        return FALSE;
    }
    // resize if necessary
    $image = new Imagick($files['tmp_name'][$key]);
    $image->scaleImage(0, 400);
    $image->writeImage($files['tmp_name'][$key]);
    #/RESIZE

    // File uploading
    if( !move_uploaded_file($files['tmp_name'][$key], $file) ) {
        continue;
    }

    #THUMBNAIL
    list($width, $height) = getimagesize($file);

    // check if the file is really an image
    if ($width == null && $height == null) {
        return FALSE;
    }

    // resize if necessary
    $image = new Imagick($file);
    $image->cropThumbnailImage(250, 250);
    $image->writeImage($file_thumb);
    #/THUMBNAIL

    // 0644 file permission activation
    if( file_exists($file) ) {
        chmod($file, 0644);
    }
}

return ( !empty($filename['image']) ) ? $filename : FALSE;
?>