<?php
/**
 * Called to handle the comment form, redirects upon success
 *
 * @param PDO $pdo
 * @param integer $postId
 * @param array $commentData
 */
function handleAddComment(PDO $pdo, $post_id, array $commentData)
{
    $errors = addCommentToPost(
        $pdo,
        $post_id,
        $commentData
    );

    // If there are no errors, redirect to the same page to show the new comment
    if (!$errors)
    {
        redirectAndExit('view-post.php?post_id=' . $post_id);
    }
    return $errors;
}

/**
 * Retrieves a single post
 *
 * @param PDO $pdo
 * @param integer $postId
 * @throws Exception
 */
function getPostRow(PDO $pdo, $post_id)
{
    $stmt = $pdo->prepare(
        'SELECT
        title, created_at, body,
        (SELECT COUNT(*) FROM comment WHERE comment.post_id = post.id) AS comment_count
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

    return $row;
}

/**
 * Writes a comment to a particular post
 *
 * @param PDO $pdo
 * @param integer $postId
 * @param array $commentData
 * @return array
 */
function addCommentToPost(PDO $pdo, $post_id, array $commentData)
{
    $errors = array();

    // Do some validation
    if (empty($commentData['name']))
    {
        $errors['name'] = 'A name is required';
    }
    if (empty($commentData['text']))
    {
        $errors['text'] = 'A comment is required';
    }

    // If we are error free, try writing the comment
    if (!$errors)
    {
        $sql = "
            INSERT INTO
                comment
            (name, website, text, post_id, created_at)
            VALUES (:name, :website, :text, :post_id, :created_at)
                
                ";
        $stmt = $pdo->prepare($sql);
        if ($stmt === false)
        {
            throw new Exception('Cannot prepare statement to add comment');
        }

        $result = $stmt->execute(
            array_merge($commentData, array('post_id' => $post_id, 'created_at' => getSqlDateForNow() ))
        );

        if ($result == false)
        {
            // @todo This renders a database-level message to the user, fix this
            $errorInfo = $stmt->errorInfo();
            if ($errorInfo)
            {
                $errors[] = $errorInfo[2];
            }
        }

    }
    // If we have errors, we return them
    return $errors;

}
/**
 * Delete the specified comment on the specified post
 *
 * @param PDO $pdo
 * @param integer $postId
 * @param integer $commentId
 * @return boolean True if the command executed without errors
 * @throws Exception
 */
function deleteComment(PDO $pdo, $postid, $commentid)
{
    // The comment id on its own would suffice, but post_id is a nice extra safety check
    $sql = "
        DELETE FROM
            comment
            WHERE
                post_id = :post_id
                AND id = :comment_id
                ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false)
    {
        throw new Exception('There was a problem preparing this query');
    }
    $result = $stmt->execute(
        array(
            'post_id'=>$postid,
            'comment_id'=>$commentid
        )
    );
    return $result !== false;
}

/**
 * Called to handle the delete comment form, redirects afterwards
 *
 * The $deleteResponse array is expected to be in the form:
 *
 *	Array ( [6] => Delete )
 *
 * which comes directly from input elements of this form:
 *
 *	name="delete-comment[6]"
 *
 * @param PDO $pdo
 * @param integer $postId
 * @param array $deleteResponse
 */
function handleDeleteComment(PDO $pdo, $postId, array $deleteResponse)
{
    // Don't do anything if the user is not authorized
    if (isLoggedIn())
    {
        $keys = array_keys($deleteResponse);
        $deleteCommentId = $keys[0];
        if ($deleteCommentId)
        {
            deleteComment($pdo, $postId, $deleteCommentId);
        }
        redirectAndExit('view-post.php?post_id=' . $postId);
    }
}