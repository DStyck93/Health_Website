<?php

function is_blank($value): bool {
    return !isset($value) || trim($value) === '';
}

/**
 * @param string $value String being tested
 * @param int $min minimum length
 * @return bool
 */
function has_length_greater_than(string $value, int $min): bool {
    $length = strlen($value);
    return $length > $min;
}

/**
 * @param string $value String being tested
 * @param int $max maximum length
 * @return bool
 */
function has_length_less_than(string $value, int $max): bool {
    $length = strlen($value);
    return $length < $max;
}

/**
 * @param string $value String being tested
 * @param int $exact Length being tested for
 * @return bool
 */
function has_length_exactly(string $value, int $exact): bool {
    $length = strlen($value);
    return $length == $exact;
}

/**
 * Can test if a string is less than, greater than, or exactly a specific length given
 * @param string $value String being tested
 * @param array $options 'min', 'max', exact'
 * @return bool
 */
function has_length(string $value, array $options): bool {
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

/**
 * Tests if the value is contained within an array
 * @param mixed $value Value being looked for
 * @param array $set Array being looked in
 * @return bool
 */
function has_inclusion_of(mixed $value, array $set): bool {
    return in_array($value, $set);
}

/**
 * Tests if a value is not in an array
 * @param mixed $value Value being tested for
 * @param array $set Array being looked in
 * @return bool
 */
function has_exclusion_of(mixed $value, array $set): bool {
    return !in_array($value, $set);
}

/**
 * Tests if a value is in the given string
 * @param string $value Value being tested for
 * @param string $required_string String being looked in
 * @return bool
 */
function has_string(string $value, string $required_string): bool {
    return str_contains($value, $required_string);
}

/**
 * @param string $value email being tested
 * @return bool
 */
function has_valid_email_format(string $value): bool {
    $email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';
    return preg_match($email_regex, $value) === 1;
}

/**
 * Queries the database to ensure username given is unique
 * @param string $username
 * @return bool
 */
function has_unique_username(string $username): bool {
    global $db;

    $stmt = $db->prepare("SELECT username FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $user_count = mysqli_num_rows($result);
    mysqli_free_result($result);

    return $user_count === 0;
}

/**
 * Queries the database to ensure email address is unique
 * @param string $email
 * @return bool
 */
function has_unique_email(string $email): bool {
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

/**
 * Returns an array of errors (if any) found when validating a new user account.
 * @param array $user
 * @return array
 */
function validate_new_user(array $user): array {
    $errors = array();

    // Username
    if (is_blank($user['username'])) {
        $errors[] = "Username cannot be blank.";
    } elseif (!has_length($user['username'], ['min' => 5, 'max' => 50])) {
        $errors[] = "Username must be between 5 and 50 characters.";
    } elseif (!has_unique_username($user['username'])) {
        $errors[] = "Username already exists.";
    }

    // Email
    if (is_blank($user['email'])) {
        $errors[] = "Email cannot be blank.";
    } elseif (!has_length($user['email'], ['max' => 100])) {
        $errors[] = "Email cannot be greater than 100 characters.";
    } elseif (!has_valid_email_format($user['email'])) {
        $errors[] = "Invalid email format.";
    } elseif (!has_unique_email($user['email'])) {
        $errors[] = "Email already exists.";
    }

    // Password
    if (is_blank($user['password'])) {
        $errors[] = "Password cannot be blank.";
    } elseif (!has_length($user['password'], ['min' => 5, 'max' => 20])) {
        $errors[] = "Password must be between 5 and 20 characters.";
    } elseif (!preg_match('/[A-Z]/', $user['password'])) {
        $errors[] = "Password must contain at least 1 uppercase letter";
    } elseif (!preg_match('/[a-z]/', $user['password'])) {
        $errors[] = "Password must contain at least 1 lowercase letter";
    } elseif (!preg_match('/[0-9]/', $user['password'])) {
        $errors[] = "Password must contain at least 1 number";
    } elseif (!preg_match('/[^A-Za-z0-9\s]/', $user['password'])) {
        $errors[] = "Password must contain at least 1 symbol";
    }

    // Confirm Password
    if(is_blank($user['password_confirm'])) {
        $errors[] = "Confirm password cannot be blank.";
    } elseif ($user['password'] !== $user['password_confirm']) {
        $errors[] = "Password and confirm password must match.";
    }

    return $errors;
}

/**
 * Returns an array of errors (if any) found when validating an update to user profile.
 * @return array
 */
function update_user(): array {
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

        // Username
        if ($updated_user['username'] != '') {

            if (!has_length($updated_user['username'], ['min' => 5, 'max' => 50])) {
                $errors[] = "Username must be between 5 and 50 characters.";
            } elseif (!has_unique_username($updated_user['username'])) {
                $errors[] = "Username already exists.";
            }

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

        // Email
        if ($updated_user['email'] != '') {

            if (!has_length($updated_user['email'], ['max' => 100])) {
                $updated_user[] = "Email cannot be greater than 100 characters.";
            } elseif (!has_valid_email_format($updated_user['email'])) {
                $errors[] = "Invalid email format.";
            } elseif (!has_unique_email($updated_user['email'])) {
                $errors[] = "Email already exists.";
            }

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

        // Password
        if ($updated_user['new_password'] != '') {

            if (!has_length($updated_user['new_password'], ['min' => 5, 'max' => 20])) {
                $errors[] = "Password must be between 5 and 20 characters.";
            } elseif (!preg_match('/[A-Z]/', $updated_user['new_password'])) {
                $errors[] = "Password must contain at least 1 uppercase letter";
            } elseif (!preg_match('/[a-z]/', $updated_user['new_password'])) {
                $errors[] = "Password must contain at least 1 lowercase letter";
            } elseif (!preg_match('/[0-9]/', $updated_user['new_password'])) {
                $errors[] = "Password must contain at least 1 number";
            } elseif (!preg_match('/[^A-Za-z0-9\s]/', $updated_user['new_password'])) {
                $errors[] = "Password must contain at least 1 symbol";
            }

            // Confirm Password
            if(is_blank($updated_user['password_confirm'])) {
                $errors[] = "Confirm password cannot be blank.";
            } elseif ($updated_user['password'] !== $updated_user['password_confirm']) {
                $errors[] = "Password and confirm password must match.";
            }

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