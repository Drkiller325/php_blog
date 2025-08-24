<?php

function addPost(PDO $pdo, $title, $body, $userId)
{
    // prepare the SQL statement
    $sql = "
        INSERT INTO
            post
            (title, body, user_id, created_at)
            VALUES
            (:title, :body, :userId, :created_at)
            ";

    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }
    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'userId' => $userId,
            'created_at' => getSqlDateForNow(),
        )
    );
    if ($result === false) {
        throw new Exception('There was a problem running this query');
    }
    return $pdo->lastInsertId();
}

function editPost(PDO $pdo, $title, $body, $postId)
{
    // Prepare the insert querry
    $sql = "
        UPDATE
            post
        SET
            title = :title,
            body = :body
        WHERE
            id = :post_id
            ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }

    $result = $stmt->execute(
        array(
            'title' => $title,
            'body' => $body,
            'post_id' => $postId,
        )
    );
    if ($result === false)
    {
        throw new Exception('There was a problem updating this post');
    }
    return true;
}