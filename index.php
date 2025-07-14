<?php

require_once 'lib/common.php';

// Connect to the database, run a query, handle errors
$pdo = getPDO();
$stmt = $pdo->query(
        'SELECT 
            id, title, created_at, body
        FROM 
            post
        ORDER BY 
            created_at DESC'
        );
if ($stmt === false) {
    throw new exception('Error in SQL query');
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <title>A blog application</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    </head>
    <body>
        <?php require 'templates/title.php' ?>

        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <h2>
                <?php echo htmlEscape($row['title']);  ?>
            </h2>
            <div>
                <?php echo $row['created_at'] ?>
                <?php echo countCommentsForPost($row['id']); ?>
            </div>
            <p>
               <?php echo htmlEscape($row['body']) ?>
            <p>
                <a
                        href="view-post.php?post_id=<?php echo $row['id']; ?>"
                >Read more...</a>
            </p>
        <?php endwhile; ?>

    </body>
</html>