<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/style.css">
  <!-- <link href="assets/css/main.css" rel="stylesheet"> -->
    
    <?php if (isset($page_styles)): ?>
        <?php foreach ($page_styles as $style): ?>
            <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/assets/css/<?php echo $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <script>
    const URL_ROOT = "<?php echo 'http://localhost/talent-hub'; ?>";
    </script>
</head>
<body>
    <!-- شريط التنقل -->
    <?php require_once APP_ROOT . '/includes/navbar.php'; ?>
    
    <!-- عرض رسائل الخطأ -->
    <div class="container mt-3">
        <?php display_flash(); ?>
    </div>