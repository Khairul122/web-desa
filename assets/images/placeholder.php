<?php
$width = isset($_GET['w']) ? (int)$_GET['w'] : 800;
$height = isset($_GET['h']) ? (int)$_GET['h'] : 400;
$text = isset($_GET['text']) ? $_GET['text'] : 'Placeholder';
$bg = isset($_GET['bg']) ? $_GET['bg'] : '6c757d';
$fg = isset($_GET['fg']) ? $_GET['fg'] : 'ffffff';

header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<svg width="<?php echo $width; ?>" height="<?php echo $height; ?>" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="#<?php echo $bg; ?>"/>
    <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="24" fill="#<?php echo $fg; ?>" 
          text-anchor="middle" dominant-baseline="middle"><?php echo htmlspecialchars($text); ?></text>
</svg>
