<?php
/**
 * @var $pdo PDO
 * @var $post_id integer
 * @var $commentCount integer
 */
?>
<form
    action="view-post.php?action=delete-comment&amp;post_id=<?php echo $post_id; ?>"
    method="post"
    class="comment-list"
>
    <h3><?php echo $commentCount?> comments</h3>

    <?php foreach (getCommentsForPost($pdo, $post_id) as $comment): ?>
        <div class="comment">
            <div class="comment-meta">
                Comment from
                <?php echo htmlEscape($comment['name']) ?>
                on
                <?php echo convertSqlDate($comment['created_at']) ?>
                <?php if (isLoggedIn()): ?>
                    <input
                    type="submit"
                    name="delete-comment[<?php echo $comment['id'] ?>]"
                    value="Delete comment"
                    />
                <?php endif; ?>
            </div>
            <div class="comment-body">
                <?php //this is already escaped ?>
                <?php echo convertNewlinesToParagraphs($comment['text']) ?>
            </div>
        </div>
    <?php endforeach; ?>
</form>


