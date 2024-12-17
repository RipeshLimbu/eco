<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($page_title) ? $page_title . " - " : ""; ?>EcoManage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/common.css">
    <?php
    // Get current file name without extension
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    $css_file = dirname($_SERVER['PHP_SELF']) . "/{$current_page}.css";
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $css_file)) {
        echo '<link rel="stylesheet" href="' . $css_file . '">';
    }
    ?>
</head>
<body class="bg-light">
</rewritten_file> 