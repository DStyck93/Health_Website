<?php if(!isset($page_title)) { $page_title = 'Diet & Exercise'; }
?>

<!doctype html>

<html lang="en">
<head>
    <title><?php echo "Health: " . h($page_title); ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?php echo url_for('/stylesheets/users.css') . '?version=10'; ?>" />
</head>
<body>
