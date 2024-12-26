<?php
    if (isset($_GET['img'])) {
        $s3Url = 'https://buytofill-items.s3.amazonaws.com/'.$_GET['img'];
        header('Content-Type: image');
        readfile($s3Url);
    } else {
        header('Location: .');
    }
    exit;
?>
