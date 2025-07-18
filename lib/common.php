<?php

/**
 * Gets the root path of the project
 *
 * @return string
 */
function getRootPath()
{
    return realpath(__DIR__ . '/..');
}

/**
 * Gets the full path for the database file
 *
 * @return string
 */
function getDatabasePath()
{
    return getRootPath() . '/data/data.sqlite';
}

/**
 * Gets the DSN for the SQLite connection
 *
 * @return string
 */
function getDsn()
{
    return 'sqlite:' . getDatabasePath();
}

/**
 * Gets the PDO connection to the database
 *
 * @return PDO
 */
function getPDO()
{
    return new PDO(getDsn());
}

/**
 * Escapes HTML so it is safe to output
 *
 * @param string $html
 * @return string
 */
function htmlEscape($html)
{
    return htmlspecialchars($html, ENT_HTML5, 'UTF-8');
}

function convertSqlDate($sqlDate)
{
    /* @var $date DateTime */
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $sqlDate);
    return $date->format('d M Y, H:i');
}

function getSqlDateForNow()
{
    return date('Y-m-d H:i:s');
}

/**
 * Converts unsafe text to safe, paragraphed, HTML
 *
 * @param string $text
 * @return string
 */
function convertNewlinesToParagraphs($text)
{
    $escaped = htmlEscape($text);
    return '<p>' . str_replace("\n", "</p><p>", $escaped) . '</p>';
}

function redirectAndExit($script)
{
    // Get the domain-relative URL (e.g. /blog/whatever.php or /whatever.php) and work
    // out the folder (e.g. /blog/ or /).
    $relativeUrl = $_SERVER['PHP_SELF'];
    $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);
    // Redirect to the full URL (http://myhost/blog/script.php)
    $host = $_SERVER['HTTP_HOST'];
    $fullUrl = 'http://' . $host . $urlFolder . $script;
    header('Location: ' . $fullUrl);
    exit();
}

/**
 * Returns the number of comments for the specified post
 *
 * @param integer $postId
 * @return integer
 */

function countCommentsForPost($postId)
{
    $pdo = getPDO();
    $sql = "
        SELECT 
            COUNT(*) c
        FROM 
            comment 
        WHERE 
            post_id = :post_id
            ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }
    $result = $stmt->execute(array('post_id' => $postId, ));
    if ($result === false) {
        throw new Exception('There was a problem running this query');
    }
    return (int) $stmt->fetchColumn();
}

/**
 * Returns the comments for the specified post
 *
 * @param integer $postId
 */
function getCommentsForPost($postId)
{
    $pdo = getPDO();
    $sql = "
        SELECT 
            id, name, text, created_at, website
        FROM 
            comment 
        WHERE 
            post_id = :post_id
        ";
    $stmt = $pdo->prepare($sql);
    if ($stmt === false) {
        throw new Exception('There was a problem preparing this query');
    }
    $result = $stmt->execute(array('post_id' => $postId, ));
    if ($result === false) {
        throw new Exception('There was a problem running this query');
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}