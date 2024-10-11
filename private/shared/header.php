<?php if(!isset($page_title)) { $page_title = 'Diet & Exercise'; } ?>

<!doctype html>

<html lang="en">
<head>
    <title><?php echo "Health: " . h($page_title); ?></title>
    <meta charset="utf-8">
    <meta name="keywords" content="health, diet, exercise">
    <meta name="description" content="Diet and exercise tracker">
    <meta name="author" content="D.Styx">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?php echo url_for('/stylesheets/style.css') . '?version=1.1'; ?>" />
</head>
<body>
