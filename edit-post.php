<?php
require_once 'lib/common.php';
require_once 'lib/edit-post.php';
require_once 'lib/view-post.php';

session_start();

// Check if the user is logged in
if (!isLoggedIn())
{
    redirectAndExit('index.php');
}
// empty defaults
$title = $body = '';

// Init database and get handle
$pdo = getPDO();

$postId = null;
if (isset($_GET['post_id']))
{
    $post = getPostRow($pdo, $_GET['post_id']);
    if ($post)
    {
        $postId = $_GET['post_id'];
        $title = $post['title'];
        $body = $post['body'];
    }
}

// Handle the post operation here
$errors = array();
if ($_POST)
{
    // Validate these first
    $title = $_POST['post-title'];
    if (!$title)
    {
        $errors[] = 'Title is required';
    }
    $body = $_POST['post-body'];
    if (!$body)
    {
        $errors[] = 'Body is required';
    }
    if (!$errors)
    {
        $pdo = getPDO();
        // Decide if we are editing or adding
        if ($postId)
        {
            editPost($pdo, $title, $body, $postId);
        }
        else
        {
            $userId = getAuthUserId($pdo);
            $postId = addPost($pdo, $title, $body, $userId);

            if ($postId === false)
            {
                $errors[] = 'Post operation failed';
            }
        }
    }

    if (!$errors)
    {
        redirectAndExit('view-post.php?post_id=' . $postId);
    }
}


?>
<!DOCTYPE html>
<html>
    <head>
        <title>A blog application | New post</title>
        <?php require 'templates/head.php' ?>
    </head>
    <body>
        <?php require 'templates/top-menu.php' ?>

        <?php if (isset($_GET['post_id'])): ?>
            <h1>Edit Post</h1>
        <?php else: ?>
            <h1>New Post</h1>
        <?php endif?>

        <?php if ($errors): ?>
        <div class="error box">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <form method="post" class="post-form user-form">
            <div>
                <label for="post-title">Title:</label>
                <input
                    id="post-title"
                    name="post-title"
                    type="text"
                    value="<?php echo htmlEscape($title) ?>"
                />
            </div>
            <div>
                <label for="post-body">Body:</label>
                <textarea
                    id="post-body"
                    name="post-body"
                    rows="12"
                    cols="70"
                ><?php echo htmlEscape($body) ?></textarea>
            </div>
            <div>
                <input
                    type="submit"
                    value="Save post"
                />
                <a href="index.php">Cancel</a>
            </div>
        </form>
    </body>
</html>