<?php
// ********** User **********

function add_user($user): array|bool {
    global $db;

    // Validate user inputs
    $errors = validate_new_user($user);
    if (!empty($errors)) {
        return $errors;
    }

    // Hash Password
    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);

    // Run Query
    $stmt = $db -> prepare("INSERT INTO users (username, email, password, timezone) VALUES (?, ?, ?, ?)");
    $stmt -> bind_param("ssss", $user['username'], $user['email'], $hashed_password, $user['timezone']);
    $result = $stmt -> execute();
    $stmt -> close();
    return $result;
}

function remove_user(): bool {
    global $db;

    $stmt = $db -> prepare("DELETE FROM users WHERE user_id = ?");
    $stmt -> bind_param("i", $_COOKIE['user_id']);
    $result = $stmt -> execute();
    $stmt -> close();
    return $result;
}

function update_user_query(string $update_type, string $update_value): bool {
    global $db;

    $stmt = $db -> prepare("UPDATE users SET $update_type = ? WHERE user_id = ?");
    $stmt -> bind_param("ss", $update_value, $_COOKIE['user_id']);
    $result = $stmt -> execute();
    $stmt -> close();
    return $result;
}

function find_user_by_username($username): false|array|null {
    global $db;

    $stmt = $db -> prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt -> bind_param("s", $username);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $stmt -> close();
    $user = $result -> fetch_assoc();
    mysqli_free_result($result);
    return $user;
}

function find_user_by_id($id): false|array|null {
    global $db;

    $stmt = $db -> prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
    $stmt -> bind_param("i", $id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $stmt -> close();
    $user = $result -> fetch_assoc();
    mysqli_free_result($result);
    return $user;
}

// ********** Food **********

function find_all_food(): false|mysqli_result {
    global $db;

    $stmt = $db -> prepare("SELECT * FROM food ORDER BY food_name ASC");
    $stmt -> execute();
    $result = $stmt -> get_result();
    $stmt -> close();
    return $result;
}

function find_food_by_name($name): false|mysqli_result {
    global $db;
    $name = '%' . $name . '%';

    $stmt = $db -> prepare("SELECT * FROM food WHERE food_name LIKE ? 
                   ORDER BY CHAR_LENGTH(food_name) ASC, food_name ASC LIMIT 50;");
    $stmt -> bind_param("s", $name);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $stmt -> close();
    return $result;
}

function find_food_by_id($id): false|array|null {
    global $db;

    $stmt = $db -> prepare("SELECT * FROM food WHERE food_id = ? LIMIT 1");
    $stmt -> bind_param("i", $id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $stmt -> close();
    $food = $result -> fetch_assoc();
    mysqli_free_result($result);
    return $food;
}

/**
 * Used to build food list for a specific user. Takes into account standard and custom food.
 * @param string $time_frame 'day', 'week', or 'month'
 * @return array An associative array of the food sorted by date added descending.
 */
function find_food_by_user(string $time_frame): array {
    global $db;
    $user_id = $_COOKIE['user_id'];

    // Standard Food
    $base_stmt = "SELECT food.food_name, food.fat, food.carb, food.protein, user_food.date_added, user_food.servings, 
        user_food.item_id 
        FROM food 
        INNER JOIN user_food ON user_food.food_id = food.food_id 
        INNER JOIN users ON users.user_id = user_food.user_id 
        WHERE users.user_id = $user_id AND user_food.date_added >= ";

    if ($time_frame == 'day' || $time_frame == '') {
        $stmt = $db ->prepare($base_stmt . "CURRENT_DATE();");

    } else if ($time_frame == 'week') {
        $stmt = $db ->prepare($base_stmt . "WEEK(CURRENT_DATE());");

    } else {
        $stmt = $db ->prepare($base_stmt . "MONTH(CURRENT_DATE());");
    }
    $stmt -> execute();
    $standard_result = $stmt -> get_result();
    $stmt -> close();

    // Custom Food
    $base_stmt = "SELECT custom_food.cf_name, custom_food.fat, custom_food.carbs, custom_food.protein, 
        user_food.date_added, user_food.servings, user_food.item_id  
        FROM custom_food  
        INNER JOIN user_food ON user_food.custom_food_id = custom_food.cf_id 
        INNER JOIN users ON users.user_id = user_food.user_id 
        WHERE users.user_id = $user_id AND ";

    if ($time_frame == 'day') {
        $stmt = $db ->prepare($base_stmt . "CURRENT_DATE();");

    } else if ($time_frame == 'week' || $time_frame == '') {
        $stmt = $db ->prepare($base_stmt . "WEEK(CURRENT_DATE());");

    } else {
        $stmt = $db ->prepare($base_stmt . "MONTH(CURRENT_DATE());");
    }
    $stmt -> execute();
    $custom_result = $stmt -> get_result();
    $stmt -> close();

    $result = array();
    foreach ($standard_result as $food) {
        $result[] = array('date_added' => $food['date_added'], 'food_name' => $food['food_name'], 'carb' => $food['carb'],
            'fat' => $food['fat'], 'protein' => $food['protein'], 'servings' => $food['servings'],
            'item_id' => $food['item_id']);
    }
    mysqli_free_result($standard_result);

    foreach ($custom_result as $food) {
        $result[] = array('date_added' => $food['date_added'], 'food_name' => $food['cf_name'], 'carb' => $food['carbs'],
            'fat' => $food['fat'], 'protein' => $food['protein'], 'servings' => $food['servings'],
            'item_id' => $food['item_id']);
    }
    mysqli_free_result($custom_result);

    array_multisort(array_column($result, 'date_added'), SORT_DESC, $result);

    return $result;
}

// TODO -- Fix timezone issue
function add_food(int $food_id, float $servings): bool {
    global $db;
    $default_timezone = "UTC";

    // Convert local time to UTC
    $local_time = date("Y-m-d H:i:s", time()); // FIXME - Not accurate


    // Add to database
    $stmt = $db -> prepare("INSERT INTO user_food (user_id, food_id, date_added, servings) VALUES (?, ?, ?, ?)");
    $stmt -> bind_param("iisd", $_COOKIE['user_id'], $food_id, $local_time, $servings);
    $result = $stmt -> execute();
    $stmt -> close();
    return $result;
}

// TODO -- Fix timezone issue
function add_custom_food(array $food): bool {
    global $db;

    // Store date as central
    date_default_timezone_set("America/Chicago");
    $date = date("Y-m-d h:m:s", time());

    // Create food item
    $stmt = $db -> prepare("INSERT INTO custom_food (user_id, cf_name, calories, carbs, fat, protein) VALUES (?, ?, ? , ?, ?, ?);");
    $stmt ->bind_param("isiiii", $_COOKIE['user_id'], $food['name'], $food['calories'], $food['carbs'], $food['fat'], $food['protein']);
    $result = $stmt -> execute();
    $stmt -> close();

    // Add to user's food history
    if ($result) {
        $food_id = mysqli_insert_id($db);

        $stmt = $db -> prepare("INSERT INTO user_food (user_id, custom_food_id, date_added, servings) VALUES (?, ?, ?, ?);");
        $stmt -> bind_param("iisd", $_COOKIE['user_id'], $food_id, $date, $food['servings']);
        $result = $stmt -> execute();
        $stmt -> close();
    }

    return $result;
}

function remove_food(int $id): bool {
    global $db;

    $stmt = $db -> prepare("DELETE FROM user_food WHERE item_id = ?");
    $stmt -> bind_param("i", $id);
    $result = $stmt -> execute();
    $stmt -> close();
    return $result;
}