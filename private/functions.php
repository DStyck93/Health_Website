<?php

use JetBrains\PhpStorm\NoReturn;

function url_for($script_path): string {
    return $script_path[0] == '/'
        ? WWW_ROOT . $script_path
        : WWW_ROOT . "/{$script_path}"; // add the leading '/' if not present
}

function u($string=""): string {
    return urlencode($string);
}

function raw_u($string=""): string {
    return rawurlencode($string);
}

function h($string=""): ?string {
    return $string == null ? null : htmlspecialchars($string);
}

#[NoReturn] function error_404(): void {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    exit();
}

#[NoReturn] function error_500(): void {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit();
}

#[NoReturn] function redirect_to($location): void {
    header("Location: " . $location);
    exit;
}

function is_post_request(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function is_get_request(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function display_message(): void {
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

function display_errors($errors=array()): string {
    $output = '';
    if(!empty($errors)) {
        $output .= "<div class=\"errors\">";
        $output .= "Error:";
        $output .= "<ul>";
        foreach($errors as $error) {
            $output .= "<li>" . h($error) . "</li>";
        }
        $output .= "</ul>";
        $output .= "</div>";
    }
    return $output;
}

function calculate_nutrition($nutrition_query): array {
    $nutrition_info = array("fat" => 0, "carb" => 0, "protein" => 0);
    foreach($nutrition_query as $food) {
        $nutrition_info['fat'] += $food['fat'];
        $nutrition_info['carb'] += $food['carb'];
        $nutrition_info['protein'] += $food['protein'];
    }
    return $nutrition_info;
}

function calculate_calories($nutrition_info): float {
    $calories = $nutrition_info['carb'] * 4;
    $calories += $nutrition_info['protein'] * 4;
    $calories += $nutrition_info['fat'] * 9;
    return $calories;
}
?>