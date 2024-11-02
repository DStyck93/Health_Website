<?php

use JetBrains\PhpStorm\NoReturn;

// ******************** Encoding ********************

function u($string=""): string {
    return urlencode($string);
}

function raw_u($string=""): string {
    return rawurlencode($string);
}

function h($string=""): ?string {
    return $string == null ? null : htmlspecialchars($string);
}

// ******************** Error Handling ********************

function error_404(): void {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    exit();
}

function error_500(): void {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit();
}

/**
 * Takes in an array of errors, and displays returns a formatted list (ul) of all the errors found.
 */
function display_errors(array $errors=array()): string {
    $output = '';
    if(!empty($errors)) {
        $output .= "<div id=\"error_message\">";
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

// ******************** Navigation ********************

/**
 * Finds a file path using the root directory
 */
function url_for(string $script_path): string {
    return $script_path[0] == '/'
        ? WWW_ROOT . $script_path
        : WWW_ROOT . "/{$script_path}"; // add the leading '/' if not present
}

/**
 * Redirects to the given URL
 */
function redirect_to($location): void {
    header("Location: " . $location);
    exit;
}

// ******************** Diet ********************

/**
 * Return a nutrition info array for the given food set.
 * $nutrition_info = [fat, carb, protein].
 */
function calculate_nutrition(array $food_set): array {
    $nutrition_info = array("fat" => 0, "carb" => 0, "protein" => 0);
    foreach($food_set as $food) {
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

// ******************** Time ********************
// MySQL DATETIME format: Y-m-d H:i:s | 2024-10-03 11:31:00

/**
 * If you can divide a Gregorian year by 4, it’s a leap year, unless it’s divisible by 100. 
 * But it is a leap year if it’s divisible by 400.
 * https://artofmemory.com/blog/how-to-calculate-the-day-of-the-week/
 */
function is_leap_year(int $year): bool {
   
    if ($year % 4 == 0) {
        if ($year % 100 == 0) {
            return $year % 400 == 0;
        }
        return true;
    }

    return false;
}

/**
 * Algorithm from https://artofmemory.com/blog/how-to-calculate-the-day-of-the-week/
 */
function get_day_of_week(DateTime $datetime): string {

    // Get Month, Date, and Year from given datetime
    $simplified_date = $datetime->format('n j Y'); // Format: 12/30/2024
    $values = explode(" ", $simplified_date);
    $month = (int)$values[0];
    $date = (int)$values[1];
    $year = $values[2];

    // Year code
    $YY = (int)substr($year, -2); // Last two digits of year
    $year_code = ($YY + ($YY / 4) % 7);

    // Month code
    $month_code = -1;
    switch($month){
        case 1: $month_code = 0; break;
        case 2: $month_code = 3; break;
        case 3: $month_code = 3; break;
        case 4: $month_code = 6; break;
        case 5: $month_code = 1; break;
        case 6: $month_code = 4; break;
        case 7: $month_code = 6; break;
        case 8: $month_code = 2; break;
        case 9: $month_code = 5; break;
        case 10: $month_code = 0; break;
        case 11: $month_code = 3; break;
        case 12: $month_code = 5; break;
    }

    // Century code (Gregorian)
    $YY = (int)substr($year, 0, 2); // First two digits of year
    $century_code = -1;
    switch($year){
        case 20: $century_code = 6; break;
        case 21: $century_code = 4; break;
        case 22: $century_code = 2; break;
        case 23: $century_code = 0; break;
    }

    // Leap year? (only matters for Jan/Feb)
    if ($month == 1 || $month == 2) {
        $is_leap_year = is_leap_year((int)$year);
        if ($is_leap_year) {
            $month_code--;
        }
    }

    // Calculate day
    $day_code = ($year_code + $month_code + $century_code + $date) % 7;
    $result = "";
    switch($day_code) {
        case 0: $result = "Sunday"; break;
        case 1: $result = "Monday"; break;
        case 2: $result = "Tuesday"; break;
        case 3: $result = "Wednesday"; break;
        case 4: $result = "Thursday"; break;
        case 5: $result = "Friday"; break;
        case 6: $result = "Saturday"; break;
    }

    // Return result
    return $result;
}

/**
 * Converts local timezone to UTC timezone
 */
function local_to_utc(DateTime $datetime): DateTime {
    return $datetime->setTimezone(new DateTimeZone('UTC'));
}

/**
 * Converts UTC timezone to local timezone
 */
function utc_to_local(DateTime $datetime): DateTime {
    $user_timezone = new DateTimeZone($_SESSION['timezone']);
    return $datetime->setTimezone($user_timezone);
}

function filter_by_time_range(array $items, string $range): array {
    $filtered_items = array();
    foreach($items as $item) {
        if(is_in_time_range($item, $range)) {
            $filtered_items[] = $item;
        }
    }

    return $filtered_items;
}

function is_in_time_range($item, string $range): bool {
    date_default_timezone_set($_SESSION['timezone']);

    // // Get Month, Day, and Year values from today and item
    $today_datetime = (new DateTime('now')) -> format('n j Y'); // Format: 12 30 2024
    $item_datetime = ($item['date_added']) -> format('n j Y');
    $today_datetime_values = explode(" ", $today_datetime);
    $item_datetime_values = explode(" ", $item_datetime);
    $today = array('month' => $today_datetime_values[0], 'day' => $today_datetime_values[1], 'year' => $today_datetime_values[2]);
    $date = array('month' => $item_datetime_values[0], 'day' => $item_datetime_values[1], 'year' => $item_datetime_values[2]);

    // Unix time for today
    $item_date = ($item['date_added']) -> format('U');

    // Month
    $seconds_in_day = 60 * 60 * 24;
    $seconds_in_week = $seconds_in_day * 7;
    $seconds_in_month = $seconds_in_day * 30;
    if ($range == 'month') {
        return (time() - $seconds_in_month) <= $item_date;

        // DEPRECIATED - Used to calculate for current month (not 30 days)
        // return $today['month'] == $date['month'] && $today['year'] == $date['year']; // Month & Year match?

    // Week (within 7 days)
    
    } else if ($range == 'week') {
        return (time() - $seconds_in_week) <= $item_date;

        // DEPRECIATED - Used to calculate for current week (not 7 days)
        // Same year/month
        // if ($today['year'] == $date['year'] && $today['month'] == $date['month']) {
        //     return (int)$today['day'] - (int)$date['day'] < 7;
        // }

        // // Within 1 month
        // if ($today['month'] == '1') { // January
        //     if($date['month'] != '12' || (int)$today['year'] - (int)$date['year'] != 1) { return false; }
        // } else { // Any other month
        //     if((int)$today['month'] - $date['month'] != 1 || $today['year'] != $date['year']) { return false; }
        // }

        // // Calculate days in month (leap year accounted for)
        // $days_in_month = 30;
        // switch((int)$date['month']) {
        //     case 2: {
        //         if(is_leap_year($date['year'])) {
        //             $days_in_month = 29; break;
        //         }
        //         $days_in_month = 28; break;
        //     }
        //     case 1:;
        //     case 3:;
        //     case 5:;
        //     case 7:;
        //     case 8:;
        //     case 10:;
        //     case 12: $days_in_month = 31; break;
        //     default: $days_in_month = 30; break;
        // }

        // // Return true if within 7 days
        // return $days_in_month - (int)$date['day'] + (int)$today['day'] < 7;

    // Day
    } else {
        return $today['month'] == $date['month'] && $today['day'] == $date['day'] && $today['year'] == $date['year']; // Month, Day, and Year match?
    }
}

// ******************** Activities ********************

// Formula provided by https://journals.lww.com/acsm-healthfitness/fulltext/2023/03000/metabolic_calculations_cases.4.aspx
function calculate_calories_burned($MET, $kg, $minutes) {
    return (float)$minutes * ((float)$MET * 3.5 * (float)$kg / 200.0);
}

// 1 lbs = 0.45359237 kg
// Reference: https://www.unitconverters.net/weight-and-mass/lbs-to-kg.htm
function convert_lbs_to_kg(float $lbs) {
    return $lbs * 0.45359237;
}

// TODO - Account for BMR
function get_total_calories_burned(array $activities) {

    $total_calories = 0;
    foreach ($activities as $activity) {
        $total_calories += $activity['calories'];
    }

    return $total_calories;
}

// ******************** Misc. ********************

/**
 * Verifies if the page is performing a POST request.
 */
function is_post_request(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

/**
 * Verifies if the page is performing a GET request.
 */
function is_get_request(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

/**
 * Displays a $_SESSION message
 */
function display_message(): void {
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

?>