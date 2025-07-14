<?php
require_once 'lib/common.php';

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
$stmt = $pdo->prepare(
    'SELECT
        title, created_at, body
    FROM
        post
    WHERE
        id = :id'
);
if ($stmt === false)
{
    throw new Exception('There was a problem preparing this query');
}
$result = $stmt->execute(
    array('id' => $post_id, )
);
if ($result === false)
{
    throw new Exception('There was a problem running this query');
}
// Let's get a row
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Swap carriage returns for paragraph breaks
$bodyText = htmlEscape($row['body']);
$paraText = str_replace("\n", '</p><p>', $bodyText);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <title>
        A blog application |
        <?php echo htmlEscape($row['title']) ?>
    </title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    </head>
    <body>
        <?php require 'templates/title.php' ?>

        <h2>
            <?php echo htmlEscape($row['title']) ?>
        </h2>
        <div>
            <?php echo $row['created_at'] ?>
        </div>
        <p>
            <?php // This is already escaped, so doesn't need further escaping ?>
            <?php echo $paraText ?>) ?>
        </p>

        <h3><?php echo countCommentsForPost($post_id) ?></h3>
        <?php foreach (getCommentsForPost($post_id) as $comment): ?>
            <div class="comment">
                <div class="comment-meta">
                    Comment By
                    <?php echo htmlEscape($comment['name']) ?>
                    on
                    <?php echo  $comment['created_at'] ?>
                </div>
                <div class="comment-body">
                    <?php echo htmlEscape($comment['text']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </body>
</html>