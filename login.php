<?php
require_once 'lib/common.php';

if (version_compare(PHP_VERSION, '5.3.0') < 0)
{
    throw new Exception('PHP version 5.3.0 or higher is required.');
}

session_start();

// redirect if logged in
if (isLoggedIn())
{
    redirectAndExit('index.php');
}

// Handle the form posting
$username = '';
if ($_POST)
{
    // Init the database
    $pdo = getPDO();

    // We redirect only if the password is correct
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ok = tryLogin($pdo, $username, $password);
    if ($ok)
    {
        login($username);
        redirectAndExit('index.php');
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            A blog application | Login
        </title>
        <?php require_once 'templates/head.php'; ?>
    </head>
    <body>
    <?php require 'templates/title.php' ?>
    <?php // If we have a username, then the user got something wrong, so let's have an error ?>
    <?php if ($username): ?>
        <div class="error box" ">
            The username or password is incorrect, try again
        </div>
    <?php endif; ?>
    <p>Login here:</p>
        <form
            method="post"
            class="user-form"
        >
            <div>
                <label for="username">
                    Username:
                </label>
                <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?php echo htmlspecialchars($username); ?>"
                />
            </div>
            <div>
                <label for="password">
                    Password:
                </label>
                <input
                        type="password"
                        id="password"
                        name="password" />
            </div>
            <input type="submit" name="submit" value="Login" />
        </form>
    </body>
</html>