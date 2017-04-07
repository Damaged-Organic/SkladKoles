<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);

if( empty($supplied_data) ) {
    return FALSE;
} else {
    list($type, $files, $item) = $supplied_data;
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

$directory = BASEPATH."[".$C_S::SUBSYSTEM_alpha."]/items/{$type}";

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

    #Next photo index, if not null
    $number_prefix = ( $key != 0 ) ? $key : "";

    if( ($type == 'rims') || ($type == 'exclusive_rims') ) {
        $filename = "{$item['brand']}_{$item['model_name']}_{$item['code']}_{$item['paint']}";
    } elseif( ($type == 'tyres') || ($type == 'exclusive_tyres') ) {
        $filename = "{$item['brand']}_{$item['model_name']}";
    }

    #BREAK IF LIMIT REACHED
    if( count(glob("?".$C_S::SUBSYSTEM_alpha."?/items/{$type}/{$filename}{.,_thumb.,_{1,2,3}.,_thumb_{1,2,3}.}{jpg,jpeg,png}", GLOB_BRACE)) >= 6 )
        return FALSE;

    #UNIX FORWARD SLASH HACK
    $filename = str_replace("/", "[slash]", $filename);
    #/UNIX FORWARD SLASH HACK

    do {
        $file       = "{$directory}/{$filename}" . (( $number_prefix ) ? "_{$number_prefix}" : "") . "." . end($input_extension_array);
        $file_thumb = "{$directory}/{$filename}_thumb" . (( $number_prefix ) ? "_{$number_prefix}" : "") . "." . end($input_extension_array);

        $number_prefix++;
    } while( file_exists($file) );

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

    $gm = $image->getImageGeometry();
    $w = $gm['width'];
    $h = $gm['height'];

    $square = FALSE;

    if($h < $w) {
        $sr = $w;
        $horz = TRUE;
    } else if($h > $w) {
        $sr = $h;
        $horz = FALSE;
    } else {
        $square = TRUE;
    }

    if(!$square && $horz) {
        $srs = $sr / 2;
        $extent_amt = $srs - ($h / 2);
        $image->extentImage($sr, $sr, 0, 0 - $extent_amt);
    } else if(!$square && !$horz) {
        $srs = $sr / 2;
        $extent_amt = $srs - ($w / 2);
        $image->extentImage($sr, $sr, 0 - $extent_amt, 0);
    }

    $image->resizeImage(250, 250, Imagick::FILTER_LANCZOS, true);

    $image->writeImage($file_thumb);
    #/THUMBNAIL

    // 0644 file permission activation
    if( file_exists($file) ) {
        chmod($file, 0644);
    }
}

return TRUE;
?>