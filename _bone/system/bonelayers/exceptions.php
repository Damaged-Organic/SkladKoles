<?php
if( !function_exists('is_access_direct') ||
    is_access_direct() )
    exit(NO_DIRECT_ACCESS);
?>
<p style="width:100%; margin:0 auto; padding:5px 0px; border:1px solid #000">
    [
    <span style='color:#ff0000; text-decoration:underline;'>
        <?php echo $error_data['name']; ?>
    </span>
    <?php
        echo "in {$error_data['path']['file']} @ line ({$error_data['path']['line']})";
        if( !empty($error_data['path']['class']) &&
            !empty($error_data['path']['type']) &&
            !empty($error_data['path']['function']) ) {
            echo " from {$error_data['path']['class']}{$error_data['path']['type']}{$error_data['path']['function']}()";
        }
    ?>
    ]
    <?php if( !empty($error_data['trace']) ): ?>
        <span style='color:#aa0000;'>
        <?php foreach($error_data['trace'] as $trace): ?>
            < |#| <?php echo $trace; ?>
        <?php endforeach; ?>
        </span>
    <?php endif; ?>
</p>