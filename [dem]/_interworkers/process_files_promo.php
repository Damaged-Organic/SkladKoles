<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    return FALSE;
} else {
    list($files, $next_max_id) = $supplied_data;
}

$mime_types = array(
    "image/jpg",
    "image/jpeg",
    "image/png",
    "image/gif"
);

$extensions = array(
    "jpg",
    "jpeg",
    "png",
    "gif"
);

$directory = BASEPATH."[".$C_S::SUBSYSTEM_alpha."]/slider";

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

    $filenames[] = $next_max_id . "." . end($input_extension_array);

    $file       = "{$directory}/{$next_max_id}." . end($input_extension_array);

    $next_max_id++;

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
    //resize
    $image = new Imagick($files['tmp_name'][$key]);
    $image->scaleImage(1000, 400);
    $image->writeImage($files['tmp_name'][$key]);
    #/RESIZE

    // File uploading
    if( !move_uploaded_file($files['tmp_name'][$key], $file) ) {
        continue;
    }

    // 0644 file permission activation
    if( file_exists($file) ) {
        chmod($file, 0644);
    }
}

return ( !empty($filenames) ) ? $filenames : FALSE;
?>