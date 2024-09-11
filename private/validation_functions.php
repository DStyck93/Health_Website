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
function has_valid_email_format($value) {
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

?>