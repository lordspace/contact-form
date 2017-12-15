<?php
/*
This is a standalone contact form script. An attempt is made to make it secure. 
Improvement suggestions are always welcome.

Usage: 
<?php include_once(dirname(__FILE__) . '/contact_form.php'); ?>

Requires: PHP 5+

Author: Svetoslav Marinov (Slavi)
Donation Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=78JCF8J6BYMTQ
Download Link: https://github.com/lordspace/contact-form/zipball/master
Blog: http://devcha.com
Main Site: http://WebWeb.ca
Version: 1.0
License: LGPL
*/

// change if the site doesn't have info@yourdomain.com
$to = 'info@' . str_ireplace('www.', '', $_SERVER['HTTP_HOST']);

$name = app_contact_get_var('name');
$email = app_contact_get_var('email');
$phone = app_contact_get_var('phone');
$subject = app_contact_get_var('subject');
$message = app_contact_get_var('message');
$contact_msg = '';

if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        if (app_contact_check_inj($value)) {
            $errors[] = "Invalid data in: $key";
        }
    }

    if (empty($name) || preg_match('#[^\w-\'\"\s]#si', $name)) {
        $errors[] = "Invalid/empty name";
    }

    // !preg_match('#^([0-9a-z]+[-._+&])*[\w-.]+@([-0-9a-z]+[.])+[a-z]{2,6}$#si', $email)	
    if (empty($email) || !filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) { 
        $errors[] = "Invalid/empty email";
    }
    
    if (!empty($phone) && preg_match('#[^\d-.()\sext:]#si', $phone)) {
        $errors[] = "Invalid/empty phone";
    }
    
    if (empty($subject) || preg_match('#[^\w-\'\"\s]#si', $subject)) {
        $errors[] = "Invalid/empty subject";
    }
    
    if (empty($message)) {
        $errors[] = "Invalid/empty message";
    }

    if (empty($errors)) {
        $headers = "From: $name <$email>\r\n";
        $headers .= "Content-type: text\r\n";
        $subject = 'Contact Form: ' . $subject;
        $message .= "\nPhone: " . $phone;

        $status = @mail($to, htmlspecialchars($subject), htmlspecialchars($message), $headers);

        if ($status) {
            $contact_msg = "<div style='color:green;'>Your message has been sent.</div>";
        } else {
            $contact_msg = "<div style='color:red;'>Cannot send  or missing data." . join("<br/>\n", $errors) . "</div>";
        }
    } else {
        $contact_msg = "<div style='color:red;'><h3>Errors: </h3>" . join("<br/>\n", $errors) . "</div>";
    }
}

/**
 * Gets a variable from the request and prints the value, empty
 */
function app_contact_get_var($val = '', $default = '') {
    return empty($_REQUEST[$val]) ? $default : trim(strip_tags($_REQUEST[$val]), ' <>');
}

/**
 * if the var conains mail fields
 */
function app_contact_check_inj($val = '') {
    return preg_match('#(\r|\n)(?:to|from|b?cc)\s*:#si', $val);
}

?>

<form name="contact_form" method="post">
    <h2>Contact Form </h2>

    <?php echo $contact_msg ?>
    
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
            <td>Name:</td>
            <td><input name="name" type="text" id="name" value="<?php echo $name ?>" size="50"></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><input name="email" type="text" id="email" value="<?php echo $email ?>" size="50"></td>
        </tr>
        <tr>
            <td>Phone:</td>
            <td><input name="phone" type="text" id="phone" value="<?php echo $phone ?>" size="50"></td>
        </tr>
        <tr>
            <td width="16%">Subject:</td>
            <td width="82%"><input name="subject" id="subject" type="text" value="<?php echo $subject ?>" size="50"></td>
        </tr>
        <tr>
            <td>Message:</td>
            <td><textarea name="message" id="message" cols="50" rows="4"><?php echo $message ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="Submit" value="Submit"></td>
        </tr>
    </table>

</form>
