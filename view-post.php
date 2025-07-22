<?php
require_once 'lib/common.php';
require_once 'lib/view-post.php';

session_start();
// Get the post ID
if (isset($_GET['post_id']))
{
    $post_id = $_GET['post_id'];
}
else
{
    // so we always have a post id
    $post_id = 0;
}
// Connect to the database, run a query, handle errors
$pdo = getPDO();
$row = getPostRow($pdo, $post_id);

// If the post does not exist, let's deal with that here
if (!$row)
{
    redirectAndExit('index.php?not-found=1');
}

$errors = null;
if ($_POST)
{
    $commentData = array(
            'name' => $_POST['comment-name'],
        'website' => $_POST['comment-website'],
        'text' => $_POST['comment-text'],
    );
    $errors = addCommentToPost($pdo, $post_id, $commentData);

    // If there are no errors, redirect to the same page to show the new comment
    if (!$errors)
    {
        redirectAndExit('view-post.php?post_id=' . $post_id);
    }
}
else
{
    $commentData = array(
        'name' => '',
        'website' => '',
        'text' => '',
    );
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>
        A blog application |
        <?php echo htmlEscape($row['title']) ?>
    </title>
    <?php require_once 'templates/head.php'; ?>
    </head>
    <body>
        <?php require 'templates/title.php' ?>

        <div class="post">
        <h2>
            <?php echo htmlEscape($row['title']) ?>
        </h2>
        <div class="date">
            <?php echo $row['created_at'] ?>
        </div>

            <?php // This is already escaped, so doesn't need further escaping ?>
            <?php echo convertNewlinesToParagraphs($row['body']) ?>
        </div>


        <h3><?php echo countCommentsForPost($pdo, $post_id) ?> comments</h3>
        <?php foreach (getCommentsForPost($pdo, $post_id) as $comment): ?>
            <div class="comment">
                <div class="comment-meta">
                    Comment By
                    <?php echo htmlEscape($comment['name']) ?>
                    on
                    <?php echo  $comment['created_at'] ?>
                </div>
                <div class="comment-body">
                    <?php // This is already escaped ?>
                    <?php echo convertNewlinesToParagraphs($comment['text']) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php require 'templates/comment-form.php' ?>
    </body>
</html>