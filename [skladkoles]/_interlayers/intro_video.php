<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);
?>
<div id="intro-picture">
    <img src="<?=$this->load_resource("images/intro-picture.jpg")?>" alt="intro-picture">
</div>
<video class="intro-video" id="intro-video" autoplay loop>
    <source src="<?=$this->load_resource("video/intro.mp4")?>" type="video/mp4">
    <source src="<?=$this->load_resource("video/intro.webm")?>" type="video/webm">
    <source src="<?=$this->load_resource("video/intro.ogv")?>" type="video/ogg">
</video>
<div id="video-play" class="fa fa-pause"></div>