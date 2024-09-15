<?php
require_once 'initialize.php';

// User

function add_user($user): array|bool {
    global $db;

    $errors = validate_new_user($user);
    if (!empty($errors)) {
        return $errors;
    }

    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);

    $stmt = $db -> prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt -> bind_param("sss", $user['username'], $user['email'], $hashed_password);
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

function update_user_query($user, $update_type, $update_value): bool {
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

// Food

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
                   ORDER BY CHAR_LENGTH(food_name) ASC, food_name ASC");
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

function find_food_by_user($user_id): false|mysqli_result {
    global $db;

    $stmt = $db ->prepare("SELECT * FROM food 
            INNER JOIN user_food ON user_food.food_id = food.food_id 
            INNER JOIN users ON users.user_id = user_food.user_id 
            WHERE users.user_id = ?
            ORDER BY date_added DESC");
    $stmt -> bind_param("i", $user_id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $stmt -> close();
    return $result;
}

function add_food($food_id, $servings): bool {
    global $db;
    $user_id = $_COOKIE['user_id'];

    $date = date("Y-m-d h:m:s", time());

    $stmt = $db -> prepare("INSERT INTO user_food (user_id, food_id, date_added, servings) VALUES (?, ?, ?, ?)");
    $stmt -> bind_param("iisd", $user_id, $food_id, $date, $servings);
    $result = $stmt -> execute();
    $stmt -> close();
    return $result;
}

function remove_food($id): bool {
    global $db;

    $stmt = $db -> prepare("DELETE FROM user_food WHERE item_id = ?");
    $stmt -> bind_param("i", $id);
    $result = $stmt -> execute();
    $stmt -> close();
    return $result;
}

/**
 * @param int $user_id
 * @param string $time_range "day", "week", or "month".
 * @return false|mysqli_result
 */
function get_user_nutrition(int $user_id, string $time_range): false|mysqli_result {
    global $db;
    $stmt = '';

    if($time_range == "day") {
        $stmt = $db ->prepare("SELECT food.fat, food.carb, food.protein FROM food 
            INNER JOIN user_food ON user_food.food_id = food.food_id 
            INNER JOIN users ON users.user_id = user_food.user_id 
            WHERE users.user_id = ? 
            AND user_food.date_added >= CURRENT_DATE();");

    } elseif ($time_range == "week") {
        $stmt = $db ->prepare("SELECT food.fat, food.carb, food.protein FROM food 
            INNER JOIN user_food ON user_food.food_id = food.food_id 
            INNER JOIN users ON users.user_id = user_food.user_id 
            WHERE users.user_id = ? 
            AND WEEK(user_food.date_added) >= WEEK(CURRENT_DATE());");

    } elseif ($time_range == "month") {
        $stmt = $db ->prepare("SELECT food.fat, food.carb, food.protein FROM food 
            INNER JOIN user_food ON user_food.food_id = food.food_id 
            INNER JOIN users ON users.user_id = user_food.user_id 
            WHERE users.user_id = ? 
            AND MONTH(user_food.date_added) >= MONTH(CURRENT_DATE());");
    }

    $stmt -> bind_param("i", $user_id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $stmt -> close();

    return $result;
}
?>