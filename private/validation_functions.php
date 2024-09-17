<?php

// is_blank('abcd')
// * validate data presence
// * uses trim() so empty spaces don't count
// * uses === to avoid false positives
// * better than empty() which considers "0" to be empty
function is_blank($value): bool {
    return !isset($value) || trim($value) === '';
}

// has_presence('abcd')
// * validate data presence
// * reverse of is_blank()
// * I prefer validation names with "has_"
function has_presence($value): bool {
    return !is_blank($value);
}

// has_length_greater_than('abcd', 3)
// * validate string length
// * spaces count towards length
// * use trim() if spaces should not count
function has_length_greater_than($value, $min): bool {
    $length = strlen($value);
    return $length > $min;
}

// has_length_less_than('abcd', 5)
// * validate string length
// * spaces count towards length
// * use trim() if spaces should not count
function has_length_less_than($value, $max): bool {
    $length = strlen($value);
    return $length < $max;
}

// has_length_exactly('abcd', 4)
// * validate string length
// * spaces count towards length
// * use trim() if spaces should not count
function has_length_exactly($value, $exact): bool {
    $length = strlen($value);
    return $length == $exact;
}

// has_length('abcd', ['min' => 3, 'max' => 5])
// * validate string length
// * combines functions_greater_than, _less_than, _exactly
// * spaces count towards length
// * use trim() if spaces should not count
function has_length($value, $options): bool {
    if(isset($options['min']) && !has_length_greater_than($value, $options['min'] - 1)) {
        return false;
    } elseif(isset($options['max']) && !has_length_less_than($value, $options['max'] + 1)) {
        return false;
    } elseif(isset($options['exact']) && !has_length_exactly($value, $options['exact'])) {
        return false;
    } else {
        return true;
    }
}

// has_inclusion_of( 5, [1,3,5,7,9] )
// * validate inclusion in a set
function has_inclusion_of($value, $set): bool {
    return in_array($value, $set);
}

// has_exclusion_of( 5, [1,3,5,7,9] )
// * validate exclusion from a set
function has_exclusion_of($value, $set): bool {
    return !in_array($value, $set);
}

// has_string('nobody@nowhere.com', '.com')
function has_string($value, $required_string): bool {
    return str_contains($value, $required_string);
}

// has_valid_email_format('nobody@nowhere.com')
// * validate correct format for email addresses
// * format: [chars]@[chars].[2+ letters]
// * preg_match is helpful, uses a regular expression
//    returns 1 for a match, 0 for no match
//    http://php.net/manual/en/function.preg-match.php
function has_valid_email_format($value): bool {
    $email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';
    return preg_match($email_regex, $value) === 1;
}

// has_unique_username('johnqpublic')
// * Validates uniqueness of username
// * For new records, provide only the username.
// * For existing records, provide current ID as second argument
//   has_unique_username('johnqpublic', 4)
function has_unique_username($username, $current_id="0"): bool {
    global $db;

    $stmt = $db->prepare("SELECT username FROM users WHERE username=? AND user_id !=?");
    $stmt->bind_param("ss", $username, $current_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $user_count = mysqli_num_rows($result);
    mysqli_free_result($result);

    return $user_count === 0;
}


// has_unique_email('johnq@public.com')
// * Validates uniqueness of email
//   has_unique_username('johnq@public.com', )
function has_unique_email($email): bool {
    global $db;

    $stmt = $db->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $user_count = mysqli_num_rows($result);
    mysqli_free_result($result);

    return $user_count === 0;
}

function validate_username($username) {
    global $errors;

    if (is_blank($username)) {
        $errors[] = "Username cannot be blank.";
    } elseif (!has_length($username, ['min' => 5, 'max' => 50])) {
        $errors[] = "Username must be between 5 and 50 characters.";
    } elseif (!has_unique_username($username)) {
        $errors[] = "Username already exists.";
    }
    return $errors;
}

function validate_email($email) {
    global $errors;

    if (is_blank($email)) {
        $errors[] = "Email cannot be blank.";
    } elseif (!has_length($email, ['max' => 100])) {
        $errors[] = "Email cannot be greater than 100 characters.";
    } elseif (!has_valid_email_format($email)) {
        $errors[] = "Invalid email format.";
    } elseif (!has_unique_email($email)) {
        $errors[] = "Email already exists.";
    }

    return $errors;
}

function validate_password($password, $password_confirm) {
    global $errors;

    if (is_blank($password)) {
        $errors[] = "Password cannot be blank.";
    } elseif (!has_length($password, ['min' => 5, 'max' => 20])) {
        $errors[] = "Password must be between 5 and 20 characters.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least 1 uppercase letter";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least 1 lowercase letter";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least 1 number";
    } elseif (!preg_match('/[^A-Za-z0-9\s]/', $password)) {
        $errors[] = "Password must contain at least 1 symbol";
    }

    // Confirm Password
    if(is_blank($password_confirm)) {
        $errors[] = "Confirm password cannot be blank.";
    } elseif ($password !== $password_confirm) {
        $errors[] = "Password and confirm password must match.";
    }

    return $errors;
}

function validate_new_user($user): array {
    global $errors;

    $errors[] = validate_username($user["username"]);
    $errors[] = validate_email($user["email"]);
    $errors[] = validate_password($user["password"], $user["password_confirm"]);

    return $errors;
}

function update_user() {
    global $errors;

    $updated_user[] = '';
    $updated_user['username'] = $_POST['username'];
    $updated_user['password'] = $_POST["password"] ?? '';
    $updated_user['email'] = $_POST['email'] ?? '';
    $updated_user['new_password'] = $_POST['new_password'] ?? '';
    $updated_user['confirm_password'] = $_POST['confirm_password'] ?? '';

    $user = find_user_by_id($_COOKIE['user_id']);

    if ($updated_user['username'] == '' && $updated_user['email'] == '' && $updated_user['new_password'] == '') {
        $errors[] = "No values entered";

    } else if ($updated_user['password'] == '') {
        $errors[] = "You must enter your password.";

    } else if (password_verify($updated_user['password'], $user['password'])) {

        if ($updated_user['username'] != '') {
            $errors = validate_username($updated_user['username']);
            if (empty($errors)) {
                $result = update_user_query('username', $updated_user['username']);
                if ($result) {
                    $_SESSION['message'] = "Username successfully updated.\n";
                    $_SESSION['username'] = $updated_user['username'];
                } else {
                    $errors[] = "An error occurred while updating your username.";
                }
            }
        }

        if ($updated_user['email'] != '') {
            $errors = validate_email($updated_user['email']);
            if (empty($errors)) {
                $result = update_user_query('email', $updated_user['email']);
                if ($result) {
                    $_SESSION['message'] = "Email successfully updated.\n";
                    $_SESSION['email'] = $updated_user['email'];
                } else {
                    $errors[] = "An error occurred while updating your email.";
                }
            }
        }

        if ($updated_user['new_password'] != '') {
            $errors = validate_password($updated_user['new_password'], $updated_user['confirm_password']);
            if (empty($errors)) {
                $hashed_password = password_hash($updated_user['new_password'], PASSWORD_DEFAULT);
                $result = update_user_query('password', $hashed_password);
                if ($result) {
                    $_SESSION['message'] = "Password successfully updated.\n";
                } else {
                    $errors[] = "An error occurred while updating your password.";
                }
            }
        }

    } else {
        $errors[] = "Invalid password";
    }

    return $errors;
}
?>