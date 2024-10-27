<?php
define("USER_MAX_WEIGHT", 2_000);

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

    $username = $user['username'];
    $email = $user['email'];
    $weight = $user['weight'];
    $password = $user['password'];
    $password_confirm = $user['password_confirm'];

    // Username
    if (is_blank($username)) {
        $errors[] = "Username cannot be blank.";
    } 
    elseif (!has_length($username, ['min' => 5, 'max' => 50])) {
        $errors[] = "Username must be between 5 and 50 characters.";
    } 
    elseif (!has_unique_username($username)) {
        $errors[] = "Username already exists.";
    }

    // Email
    if (is_blank($email)) {
        $errors[] = "Email cannot be blank.";
    } 
    elseif (!has_length($email, ['max' => 100])) {
        $errors[] = "Email cannot be greater than 100 characters.";
    } 
    elseif (!has_valid_email_format($email)) {
        $errors[] = "Invalid email format.";
    } 
    elseif (!has_unique_email($email)) {
        $errors[] = "Email already exists.";
    }

    // Weight
    if (is_blank($weight)) {
        $errors[] = "Weight cannot be blank. It is needed for calculating caloric expenditures.";
    }
    elseif ($weight > USER_MAX_WEIGHT) {
        $errors[] = "Weight cannot be greater than " . USER_MAX_WEIGHT . " lbs.";
    }
    elseif ($weight <= 0) {
        $errors[] = "Weight must be greater than 0. It is needed for calculating caloric expenditures.";
    }

    // Password
    if (is_blank($password)) {
        $errors[] = "Password cannot be blank.";
    } 
    elseif (!has_length($password, ['min' => 5, 'max' => 20])) {
        $errors[] = "Password must be between 5 and 20 characters.";
    } 
    elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least 1 uppercase letter";
    } 
    elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least 1 lowercase letter";
    } 
    elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least 1 number";
    } 
    elseif (!preg_match('/[^A-Za-z0-9\s]/', $password)) {
        $errors[] = "Password must contain at least 1 symbol";
    }

    // Confirm Password
    if(is_blank($password_confirm)) {
        $errors[] = "Confirm password cannot be blank.";
    } 
    elseif ($password !== $password_confirm) {
        $errors[] = "Passwords don't match.";
    }

    return $errors;
}

/**
 * Returns an array of errors (if any) found when validating an update to user profile.
 * @return array
 */
function update_user(): array {
    global $errors;

    $username = $_POST['username'];
    $email = $_POST['email'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $timezone = $_POST['timezone'] ?? '';
    $old_password = $_POST["password"] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $user = find_user_by_id($_COOKIE['user_id']);

    if ($username == '' && $email == '' && $new_password == '' && $timezone == $_SESSION['timezone']) {
        $errors[] = "No values entered";

    } else if ($old_password == '') {
        $errors[] = "You must enter your password.";

    } else if (password_verify($old_password, $user['password'])) {

        // Username
        if ($username != '') {

            // Validate Length
            if (!has_length($username, ['min' => 5, 'max' => 50])) {
                $errors[] = "Username must be between 5 and 50 characters.";
            } 
            
            // Validate uniqueness
            elseif (!has_unique_username($username)) {
                $errors[] = "Username already exists.";
            }

            // No errors, update user
            if (empty($errors)) {
                $result = update_user_query('username', $username);
                if ($result) {
                    $_SESSION['message'] = "Username successfully updated.\n";
                    $_SESSION['username'] = $username;
                } else {
                    $errors[] = "An error occurred while updating your username.";
                }
            }
        }

        // Email
        if ($email != '') {

            // Validate Length
            if (!has_length($email, ['max' => 100])) {
                $errors[] = "Email cannot be greater than 100 characters.";
            } 
            
            // Validate Format
            elseif (!has_valid_email_format($email)) {
                $errors[] = "Invalid email format.";
            } 
            
            // Validate Uniqueness
            elseif (!has_unique_email($email)) {
                $errors[] = "Email already exists.";
            }

            // No errors, update user
            if (empty($errors)) {
                $result = update_user_query('email', $email);
                if ($result) {
                    $_SESSION['message'] = "Email successfully updated.\n";
                    $_SESSION['email'] = $email;
                } else {
                    $errors[] = "An error occurred while updating your email.";
                }
            }
        }

        // Weight
        if ($weight != '') {

            // Validate Max Weight
            if ($weight > USER_MAX_WEIGHT) {
                $errors[] = "Max weight is " . USER_MAX_WEIGHT . " lbs.";
            }
            
            // Validate Min Weight
            elseif ($weight <= 0) {
                $errors[] = "Weight must be greater than 0 lbs.";
            }

            // No Errors, update user
            if (empty($errors)) {
                $result = update_user_query('weight_pounds', $weight);
                if ($result) {
                    $_SESSION['message'] = "Weight was successfully updated.\n";
                    $_SESSION['weight'] = $weight;
                } else {
                    $errors[] = "An error occurred while updating your weight.";
                }
            }
        }

        // Timezone
        if ($timezone != '') {
            $result = update_user_query('timezone', $timezone);
            if ($result) {
                $_SESSION['message'] = "Timezone successfully updated.\n";
                $_SESSION['timezone'] = $timezone;
            } else {
                $errors[] = "An error occurred while updating your timezone.";
            }
        }

        // Password
        if ($new_password != '') {

            // Validate password
            if (!has_length($new_password, ['min' => 5, 'max' => 20])) {
                $errors[] = "Password must be between 5 and 20 characters.";
            } elseif (!preg_match('/[A-Z]/', $new_password)) {
                $errors[] = "Password must contain at least 1 uppercase letter";
            } elseif (!preg_match('/[a-z]/', $new_password)) {
                $errors[] = "Password must contain at least 1 lowercase letter";
            } elseif (!preg_match('/[0-9]/', $new_password)) {
                $errors[] = "Password must contain at least 1 number";
            } elseif (!preg_match('/[^A-Za-z0-9\s]/', $new_password)) {
                $errors[] = "Password must contain at least 1 symbol";
            }

            // Validate Confirm Password
            if(is_blank($confirm_password)) {
                $errors[] = "Confirm password cannot be blank.";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "Password and confirm password must match.";
            }

            // No errors, submit change
            if (empty($errors)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
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