<?php
    session_start();
    include "../../db/db.php";
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../login/admin_login.php");
        exit();
    }
?>

<?php
$appBaseUrl = str_replace('\\', '/', dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))));
$appBaseUrl = ($appBaseUrl === '/' || $appBaseUrl === '\\') ? '' : rtrim($appBaseUrl, '/');

define('BOOK_IMAGE_MAX_BYTES', 2 * 1024 * 1024);
define('BOOK_PDF_MAX_BYTES', 10 * 1024 * 1024);
define('TEMP_UPLOAD_TTL', 3600);

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function normalize_relative_path($relativePath)
{
    return ltrim(str_replace('\\', '/', (string) $relativePath), '/');
}

function relative_absolute_path($projectRoot, $relativePath)
{
    $relativePath = normalize_relative_path($relativePath);
    if ($relativePath === '' || preg_match('#(^|/)\.\.(/|$)#', $relativePath)) {
        return null;
    }

    return $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
}

function app_url($relativePath, $appBaseUrl)
{
    $relativePath = normalize_relative_path($relativePath);
    return ($appBaseUrl === '' ? '' : $appBaseUrl) . '/' . $relativePath;
}

function ensure_directory($directory)
{
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create an upload directory.');
    }
}

function format_bytes($bytes)
{
    $units = array('B', 'KB', 'MB', 'GB');
    $bytes = max(0, (int) $bytes);
    $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
    $power = min($power, count($units) - 1);
    $value = $bytes / pow(1024, $power);

    return number_format($value, $power === 0 ? 0 : 1) . ' ' . $units[$power];
}

function sanitize_upload_name($name)
{
    $name = basename((string) $name);
    $name = preg_replace('/[\x00-\x1F\x7F]+/', '', $name);

    return $name !== '' ? $name : 'file';
}

function collect_book_form_values($source)
{
    return array(
        'name' => trim((string) ($source['name'] ?? '')),
        'category' => trim((string) ($source['category'] ?? '')),
        'author' => trim((string) ($source['author'] ?? '')),
        'description' => trim((string) ($source['description'] ?? '')),
    );
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token()
{
    return $_SESSION['csrf_token'];
}

function csrf_input()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function require_valid_csrf()
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');

    if ($sessionToken === '' || $submittedToken === '' || !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(403);
        echo 'Invalid CSRF token.';
        exit();
    }
}

function session_temp_relative_dir()
{
    $sessionId = preg_replace('/[^a-zA-Z0-9]/', '', session_id());
    return $sessionId !== '' ? 'uploads/tmp/' . $sessionId : '';
}

function unique_storage_name($extension)
{
    $extension = ltrim((string) $extension, '.');
    $baseName = str_replace('.', '', uniqid('', true)) . '-' . bin2hex(random_bytes(8));

    return $extension !== '' ? $baseName . '.' . $extension : $baseName;
}

function store_uploaded_file($fieldName, $targetRelativeDirectory, $projectRoot, $allowedMimes, $maxBytes, $required = false)
{
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        if ($required) {
            throw new RuntimeException(ucfirst($fieldName) . ' file is required.');
        }
        return null;
    }

    $file = $_FILES[$fieldName];
    $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($errorCode === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            throw new RuntimeException(ucfirst($fieldName) . ' file is required.');
        }
        return null;
    }

    if ($errorCode !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Unable to upload the ' . $fieldName . ' file.');
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size < 1) {
        throw new RuntimeException('The ' . $fieldName . ' file is empty.');
    }

    if ($size > $maxBytes) {
        throw new RuntimeException(ucfirst($fieldName) . ' must be ' . format_bytes($maxBytes) . ' or smaller.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
    if ($finfo) {
        finfo_close($finfo);
    }

    if ($mime === false || !isset($allowedMimes[$mime])) {
        throw new RuntimeException('Invalid ' . $fieldName . ' type.');
    }

    $targetAbsoluteDirectory = relative_absolute_path($projectRoot, $targetRelativeDirectory);
    if ($targetAbsoluteDirectory === null) {
        throw new RuntimeException('Invalid upload directory.');
    }

    ensure_directory($targetAbsoluteDirectory);

    $storageName = unique_storage_name($allowedMimes[$mime]);
    $relativeTargetPath = normalize_relative_path($targetRelativeDirectory) . '/' . $storageName;
    $absoluteTargetPath = relative_absolute_path($projectRoot, $relativeTargetPath);

    if ($absoluteTargetPath === null || !move_uploaded_file($file['tmp_name'], $absoluteTargetPath)) {
        throw new RuntimeException('Failed to save the ' . $fieldName . ' file.');
    }

    return array(
        'path' => $relativeTargetPath,
        'stored_name' => $storageName,
    );
}

function delete_relative_file($projectRoot, $relativePath)
{
    $absolutePath = relative_absolute_path($projectRoot, $relativePath);
    if ($absolutePath !== null && is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

function remove_session_temp_dir_if_empty($projectRoot)
{
    $relativeDir = session_temp_relative_dir();
    if ($relativeDir === '') {
        return;
    }

    $absoluteDir = relative_absolute_path($projectRoot, $relativeDir);
    if ($absoluteDir === null || !is_dir($absoluteDir)) {
        return;
    }

    $items = array_diff(scandir($absoluteDir) ?: array(), array('.', '..'));
    if ($items === array()) {
        @rmdir($absoluteDir);
    }
}

function clear_preview_item($key, $projectRoot)
{
    if (!empty($_SESSION['preview'][$key]['path'])) {
        delete_relative_file($projectRoot, $_SESSION['preview'][$key]['path']);
    }

    unset($_SESSION['preview'][$key]);
    remove_session_temp_dir_if_empty($projectRoot);
}

function clear_preview_state($projectRoot)
{
    foreach (array('image', 'pdf') as $key) {
        if (!empty($_SESSION['preview'][$key])) {
            clear_preview_item($key, $projectRoot);
        }
    }

    unset($_SESSION['preview_form']);
}

function sync_preview_session($projectRoot)
{
    foreach (array('image', 'pdf') as $key) {
        if (empty($_SESSION['preview'][$key]['path'])) {
            unset($_SESSION['preview'][$key]);
            continue;
        }

        $absolutePath = relative_absolute_path($projectRoot, $_SESSION['preview'][$key]['path']);
        if ($absolutePath === null || !is_file($absolutePath)) {
            unset($_SESSION['preview'][$key]);
        }
    }
}

function cleanup_expired_temp_uploads($tempRootAbsolute, $maxAgeSeconds)
{
    if (!is_dir($tempRootAbsolute)) {
        return;
    }

    $cutoff = time() - $maxAgeSeconds;
    $sessionDirectories = glob($tempRootAbsolute . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
    if ($sessionDirectories === false) {
        return;
    }

    foreach ($sessionDirectories as $sessionDirectory) {
        $files = glob($sessionDirectory . DIRECTORY_SEPARATOR . '*');
        if ($files !== false) {
            foreach ($files as $filePath) {
                $modifiedAt = is_file($filePath) ? filemtime($filePath) : false;
                if ($modifiedAt !== false && $modifiedAt < $cutoff) {
                    @unlink($filePath);
                }
            }
        }

        $remaining = array_diff(scandir($sessionDirectory) ?: array(), array('.', '..'));
        if ($remaining === array()) {
            @rmdir($sessionDirectory);
        }
    }
}

function save_temp_upload($fieldName, $sessionKey, $projectRoot, $allowedMimes, $maxBytes)
{
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return false;
    }

    $file = $_FILES[$fieldName];
    $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($errorCode === UPLOAD_ERR_NO_FILE) {
        return false;
    }

    if ($errorCode !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Unable to upload the ' . $sessionKey . ' file.');
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size < 1) {
        throw new RuntimeException('The ' . $sessionKey . ' file is empty.');
    }

    if ($size > $maxBytes) {
        throw new RuntimeException(
            ucfirst($sessionKey) . ' must be ' . format_bytes($maxBytes) . ' or smaller.'
        );
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
    if ($finfo) {
        finfo_close($finfo);
    }

    if ($mime === false || !isset($allowedMimes[$mime])) {
        throw new RuntimeException('Invalid ' . $sessionKey . ' type.');
    }

    $relativeTempDir = session_temp_relative_dir();
    $absoluteTempDir = relative_absolute_path($projectRoot, $relativeTempDir);
    if ($relativeTempDir === '' || $absoluteTempDir === null) {
        throw new RuntimeException('Unable to determine the temporary upload path.');
    }

    ensure_directory($absoluteTempDir);

    $storageName = unique_storage_name($allowedMimes[$mime]);
    $relativePath = $relativeTempDir . '/' . $storageName;
    $absolutePath = relative_absolute_path($projectRoot, $relativePath);
    if ($absolutePath === null || !move_uploaded_file($file['tmp_name'], $absolutePath)) {
        throw new RuntimeException('Failed to save the ' . $sessionKey . ' preview.');
    }

    if (!empty($_SESSION['preview'][$sessionKey]['path'])) {
        delete_relative_file($projectRoot, $_SESSION['preview'][$sessionKey]['path']);
    }

    $_SESSION['preview'][$sessionKey] = array(
        'path' => normalize_relative_path($relativePath),
        'stored_name' => $storageName,
        'original_name' => sanitize_upload_name($file['name'] ?? $storageName),
        'size' => $size,
        'mime' => $mime,
        'uploaded_at' => time(),
    );

    return true;
}

function promote_preview_to_permanent($sessionKey, $targetRelativeDirectory, $projectRoot)
{
    if (empty($_SESSION['preview'][$sessionKey]['path'])) {
        return null;
    }

    $sourceAbsolutePath = relative_absolute_path($projectRoot, $_SESSION['preview'][$sessionKey]['path']);
    if ($sourceAbsolutePath === null || !is_file($sourceAbsolutePath)) {
        throw new RuntimeException('The ' . $sessionKey . ' preview is missing. Please preview it again.');
    }

    $targetAbsoluteDirectory = relative_absolute_path($projectRoot, $targetRelativeDirectory);
    if ($targetAbsoluteDirectory === null) {
        throw new RuntimeException('Invalid permanent upload directory.');
    }

    ensure_directory($targetAbsoluteDirectory);

    $extension = strtolower(pathinfo($sourceAbsolutePath, PATHINFO_EXTENSION));
    $storageName = unique_storage_name($extension);
    $relativeTargetPath = normalize_relative_path($targetRelativeDirectory) . '/' . $storageName;
    $absoluteTargetPath = relative_absolute_path($projectRoot, $relativeTargetPath);

    if ($absoluteTargetPath === null || !copy($sourceAbsolutePath, $absoluteTargetPath)) {
        throw new RuntimeException('Failed to move the ' . $sessionKey . ' file into permanent storage.');
    }

    return array(
        'path' => $relativeTargetPath,
        'stored_name' => $storageName,
    );
}

function book_pdf_href($pdfPath, $appBaseUrl)
{
    $pdfPath = trim((string) $pdfPath);
    if ($pdfPath === '') {
        return '';
    }

    $normalizedPath = str_replace('\\', '/', $pdfPath);
    if (strpos($normalizedPath, '://') !== false || strpos($normalizedPath, '/') === 0) {
        return $normalizedPath;
    }

    if (preg_match('#^(\.\./)+assets/pdfs/#', $normalizedPath)) {
        return $normalizedPath;
    }

    if (strpos($normalizedPath, '/') === false) {
        return ($appBaseUrl === '' ? '' : $appBaseUrl) . '/assets/pdfs/' . rawurlencode($normalizedPath);
    }

    return $normalizedPath;
}

$tableSql = "CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        category VARCHAR(255) NOT NULL,
        description TEXT NULL,
        image_path VARCHAR(255) NOT NULL,
        pdf_path VARCHAR(255) NULL
    )";

if (!mysqli_query($conn, $tableSql)) {
    echo "Error creating table: " . mysqli_error($conn);
    exit();
}

$descriptionColumnSql = "SHOW COLUMNS FROM books LIKE 'description'";
$descriptionColumnResult = mysqli_query($conn, $descriptionColumnSql);
if ($descriptionColumnResult && $descriptionColumnResult->num_rows === 0) {
    if (!mysqli_query($conn, "ALTER TABLE books ADD COLUMN description TEXT NULL AFTER category")) {
        echo "Error updating books table: " . mysqli_error($conn);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
}

$projectRoot = dirname(__DIR__, 2);

$popupErrors = array();

$successMessage = isset($_GET['success']) ? 'Book added successfully.' : '';
$showPopup = isset($_GET['popup']) && $_GET['popup'] === 'add-book';
if (isset($_GET['success'])) {
    unset($_SESSION['preview'], $_SESSION['preview_form'], $_SESSION['preview_flash_errors']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['open_add_popup'])) {
    unset($_SESSION['preview'], $_SESSION['preview_form'], $_SESSION['preview_flash_errors']);
    $showPopup = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book_submit'])) {
    $showPopup = true;
    $submittedValues = collect_book_form_values($_POST);

    if (
        $submittedValues['name'] === ''
        || $submittedValues['category'] === ''
        || $submittedValues['author'] === ''
        || $submittedValues['description'] === ''
    ) {
        $popupErrors[] = 'All fields are required.';
    }

    if (!isset($_FILES['image']) || (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
        $popupErrors[] = 'Image file is required.';
    }

    if (!isset($_FILES['pdf']) || (($_FILES['pdf']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
        $popupErrors[] = 'PDF file is required.';
    }

    if ($popupErrors === array()) {
        $storedFiles = array();

        try {
            $imageFile = store_uploaded_file(
                'image',
                'assets/images',
                $projectRoot,
                array(
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ),
                BOOK_IMAGE_MAX_BYTES,
                true
            );
            $storedFiles[] = $imageFile['path'];

            $pdfFile = store_uploaded_file(
                'pdf',
                'assets/pdfs',
                $projectRoot,
                array(
                    'application/pdf' => 'pdf',
                ),
                BOOK_PDF_MAX_BYTES,
                true
            );
            $storedFiles[] = $pdfFile['path'];

            $insertSql = "INSERT INTO books (name, author, category, description, image_path, pdf_path) VALUES (?, ?, ?, ?, ?, ?)";
            $statement = mysqli_prepare($conn, $insertSql);
            if ($statement === false) {
                throw new RuntimeException('Failed to prepare the book insert statement.');
            }

            mysqli_stmt_bind_param(
                $statement,
                'ssssss',
                $submittedValues['name'],
                $submittedValues['author'],
                $submittedValues['category'],
                $submittedValues['description'],
                $imageFile['stored_name'],
                $pdfFile['stored_name']
            );

            if (!mysqli_stmt_execute($statement)) {
                $statementError = mysqli_stmt_error($statement);
                mysqli_stmt_close($statement);
                throw new RuntimeException('Failed to save the book: ' . $statementError);
            }

            mysqli_stmt_close($statement);

            unset($_SESSION['preview'], $_SESSION['preview_form'], $_SESSION['preview_flash_errors']);
            $_POST = array();
            $_FILES = array();
            header("Location: dashboard.php?success=1");
            exit();
        } catch (Throwable $exception) {
            foreach ($storedFiles as $relativePath) {
                delete_relative_file($projectRoot, $relativePath);
            }

            $popupErrors[] = $exception->getMessage();
        }
    }
}

//total books count
$totalBooksSql = "SELECT COUNT(*) AS total_books FROM books";
$totalBooksResult = mysqli_query($conn, $totalBooksSql);
$totalBooks = 0;
if ($totalBooksResult && $totalBooksResult->num_rows > 0) {
    $row = mysqli_fetch_assoc($totalBooksResult);
    $totalBooks = $row['total_books'];
}

//total categories count
$totalCategoriesSql = "SELECT COUNT(DISTINCT category) AS total_categories FROM books";
$totalCategoriesResult = mysqli_query($conn, $totalCategoriesSql);
$totalCategories = 0;
if ($totalCategoriesResult && $totalCategoriesResult->num_rows > 0) {
    $row = mysqli_fetch_assoc($totalCategoriesResult);
    $totalCategories = $row['total_categories'];
}

//total issued book count
$totalIssuedBooksSql = "SELECT COUNT(*) AS total_issued_books FROM issued_books";
$totalIssuedBooksResult = mysqli_query($conn, $totalIssuedBooksSql);
$totalIssuedBooks = 0;
if ($totalIssuedBooksResult && $totalIssuedBooksResult->num_rows > 0) {
    $row = mysqli_fetch_assoc($totalIssuedBooksResult);
    $totalIssuedBooks = $row['total_issued_books'];
}

//all books
$allBooksSql = "SELECT * FROM books";
$allBooksResult = mysqli_query($conn, $allBooksSql);
$books = [];
if ($allBooksResult && $allBooksResult->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($allBooksResult)) {
        $books[] = $row;
    }
}

//search functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_submit'])) {
    $searchTerm = $_POST['search'] ?? '';
    $searchTermEscaped = mysqli_real_escape_string($conn, $searchTerm);
    $searchSql = "SELECT * FROM books WHERE name LIKE '%$searchTermEscaped%'";
    $searchResult = mysqli_query($conn, $searchSql);
    $books = [];
    if ($searchResult && $searchResult->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($searchResult)) {
            $books[] = $row;
        }
    }
}

//delete book functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book_submit'])) {
    $deleteBookId = (int) ($_POST['delete_book_id'] ?? 0);
    if ($deleteBookId > 0) {
        $deleteSql = "DELETE FROM books WHERE id=" . $deleteBookId;
        mysqli_query($conn, $deleteSql);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_submit'])) {
    session_destroy();
    header("Location: ../login/admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="../../assets/images/logo3.png" type="image/png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap');

        :root {
            --c-brown: #8a7650;
            --c-sage: #8e977d;
            --c-sand: #dbcea5;
            --c-linen: #ece7d1;
            --c-ink: #2b2418;
            --c-white: #fffdf6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(219, 206, 165, 0.35), transparent 35%),
                linear-gradient(180deg, #f4efde 0%, #ece7d1 55%, #f5f0dd 100%);
            color: var(--c-ink);
            overflow-x: hidden;
        }

        /* .page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem 2.5rem;
        } */

        .main-navbar {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            background: var(--c-sand);
            color: var(--c-brown);
            padding: 1rem 1.5rem;
            border: 2px solid var(--c-linen);
        }

        .brand-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-frame {
            width: 52px;
            height: 52px;
            border: 2px solid var(--c-linen);
            border-radius: 50%;
            background: var(--c-sand);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .logo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-name {
            font-size: 1.25rem;
            letter-spacing: 0.04em;
            margin: 0;
        }

        .nav-username {
            justify-self: center;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin: 0;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatar-frame {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid var(--c-linen);
            background: var(--c-sand);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .avatar-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .exit-btn {
            background: transparent;
            color: var(--c-brown);
            border: 2px solid var(--c-brown);
            padding: 0.5rem 0.85rem;
            border-radius: 50px;
            font-weight: 700;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .exit-btn:hover {
            background: var(--c-linen);
            color: var(--c-brown);
        }

        .content {
            padding:2rem 1rem;
        }

        /* grid containing the cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            align-items: stretch;
            /* make cards equal height */
            margin-bottom: 1.25rem;
        }
        /* force grid items (forms/links) to stretch */
        .stat-grid > form,
        .stat-grid > a {
            display: block;
            height: 100%;
        }
        .stat-grid > a .card {
            height: 100%;
        }
        /* ensure inline forms/buttons fill the grid cell height */
        .stat-grid .inline-form {
            height: 100%;
            display: block;
        }
        .stat-grid .inline-form .card {
            height: 100%;
        }
        .stat-grid .inline-form .card-button {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }

        /* numbers and labels */
        .card-number {
            font-size: 2rem;
            color: var(--c-brown);
            font-weight: 700;
            margin: 0 0 .25rem;
        }

        /* make the button-as-card maintain the card visuals */
        .card-label {
            margin: 0;
            color: #4d4638;
            font-weight: 600;
            letter-spacing: 0.02em;
            margin-top: 0.5rem;
        }



        /* remove native button chrome but keep .card visuals */
        .card-button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: none;
            border: none;
            width: 100%;
            padding: 0;
            cursor: pointer;
            font: inherit;
            color: inherit;
        }

        /* dashed add-card style (special) */
        .add-card {
            border-style: none;
            background: linear-gradient(135deg, rgba(142, 151, 125, 0.12), rgba(219, 206, 165, 0.18));
            cursor: pointer;
        }

        /* hover / focus lift */
        .card:hover,
        .card:focus {
            transform: translateY(-6px);
            border-style: dashed;
            border-color: var(--c-brown);
            box-shadow: 0 18px 40px rgba(43, 36, 24, 0.14);
        }

        /* base card look */
        .card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* center content vertically */
            align-items: center;
            min-height: 120px;
            /* consistent height */
            padding: 1.25rem 1.25rem;
            text-align: center;
            background: var(--c-white);
            border: 2px solid rgba(219, 206, 165, 0.85);
            /* soft sand border */
            border-radius: 14px;
            box-shadow: 0 8px 18px rgba(43, 36, 24, 0.08);
            /* soft default shadow */
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
            overflow: hidden;
        }

        /* plus icon as span (same visuals as your prior pseudo elements) */
        .plus-icon {
            display: inline-block;
            width: 48px;
            height: 48px;
            margin-bottom: .5rem;
            position: relative;
        }

        .plus-icon::before,
        .plus-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            background: var(--c-brown);
            transform: translate(-50%, -50%);
        }

        .plus-icon::before {
            width: 40px;
            height: 3px;
        }

        .plus-icon::after {
            width: 3px;
            height: 40px;
        }

        .file-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .divider-stack {
            margin: 2rem 0 1.25rem;
            display: grid;
            gap: 0.6rem;
        }

        .divider-stack span {
            display: block;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(138, 118, 80, 0.7), rgba(219, 206, 165, 0.9));
        }

        .divider-stack span:nth-child(1) {
            width: 94%;
        }

        .divider-stack span:nth-child(2) {
            width: 88%;
            justify-self: center;
        }

        .divider-stack span:nth-child(3) {
            width: 82%;
            justify-self: end;
        }

        .list-section {
            background: var(--c-white);
            border: 2px solid var(--c-sand);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 12px 26px rgba(43, 36, 24, 0.08);
        }

        .list-header {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .intro {
            margin: 0;
            color: #4a4236;
            font-weight: 600;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .searchInput {
            flex: 1 1 240px;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 2px solid var(--c-sand);
            background: #f8f3e3;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .searchInput:focus {
            border-color: var(--c-brown);
            box-shadow: 0 0 0 3px rgba(138, 118, 80, 0.2);
        }

        .primary-btn {
            background: var(--c-sage);
            color: #ffffff;
            border: none;
            padding: 0.78rem 1.4rem;
            border-radius: 10px;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .primary-btn:hover {
            background: #7e8a6f;
            transform: translateY(-1px);
        }

        .table-wrap {
            margin-top: 1.25rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
            background: var(--c-white);
        }

        thead th {
            background: var(--c-sage);
            color: var(--c-white);
            text-align: left;
            padding: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        tbody td {
            padding: 0.85rem;
            border-bottom: 1px solid rgba(219, 206, 165, 0.6);
            color: #3d3529;
        }

        tbody tr:nth-child(even) {
            background: rgba(236, 231, 209, 0.45);
        }

        tbody tr:hover {
            background: rgba(142, 151, 125, 0.1);
        }

        .table-body button {
            background: transparent;
            border: 1.5px solid var(--c-brown);
            color: var(--c-brown);
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .table-body button:hover {
            background: var(--c-brown);
            color: var(--c-linen);
        }

        a {
            color: var(--c-brown);
            text-decoration: none;
            font-weight: 700;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 720px) {
            .main-navbar {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .nav-actions {
                justify-content: center;
            }
        }


        /* Overlay */
        .popup-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        /* Popup Container */
        .popup-container {
            background-color: var(--c-linen);
            padding: 24px 26px;
            /* reduced padding */
            width: 100%;
            max-width: 440px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        /* Form */
        .popup-form {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #2b2a2a;
        }

        /* Labels */
        .form-label {
            display: block;
            margin-bottom: 4px;
            /* tighter */
            font-weight: 500;
        }

        /* Text Inputs */
        .text-input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid var(--c-sand);
            font-size: 14px;
        }

        .text-input:focus {
            outline: none;
            border-color: var(--c-sage);
        }

        textarea.text-input {
            min-height: 120px;
            resize: vertical;
        }

        /* Hide default file input */
        .file-input {
            display: none;
        }

        /* Large upload container */
        .file-box {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 130px;
            /* slightly smaller */
            margin: 12px 0;
            /* fixed spacing */
            border: 2px dashed var(--c-sage);
            border-radius: 8px;
            background-color: var(--c-sand);
            color: #5a5a5a;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            position: relative;
            overflow: hidden;
            padding: 16px;
            transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .file-box:hover {
            background-color: #d7c89a;
        }

        .file-box-content {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 100%;
            padding: 12px;
        }

        .file-box-content-pdf {
            flex-direction: row;
            justify-content: flex-start;
            gap: 10px;
            padding-right: 42px;
            white-space: nowrap;
        }

        .file-preview-image {
            width: 80px;
            height: 80px;
            border-radius: 6px;
            object-fit: contain;
        }

        .file-meta {
            display: block;
            word-break: break-word;
            max-width: 100%;
        }

        .file-caption {
            font-size: 12px;
            color: #4f4a40;
        }

        .file-preview-link,
        .file-box a {
            color: var(--c-brown);
            font-weight: 700;
        }

        .file-error-text {
            max-width: 85%;
            line-height: 1.45;
        }

        .file-remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.96);
            color: #3d3529;
            box-shadow: 0 8px 18px rgba(43, 36, 24, 0.16);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            line-height: 1;
            padding: 0;
        }

        .file-remove-btn:hover {
            background: #ffffff;
        }

        .file-remove-btn:focus-visible {
            outline: 2px solid var(--c-brown);
            outline-offset: 2px;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            margin-top: 14px;
            /* clear separation */
            padding: 12px;
            color: var(--c-linen);
            border: none;
            border-radius: 7px;
            font-size: 15px;
            cursor: pointer;
        }

        .submit-btn {
            background-color: var(--c-brown);
        }

        .submit-btn:hover {
            background-color: var(--c-sage);
        }

        .status-message {
            margin: 0 0 1rem;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        .status-message.success {
            background: rgba(142, 151, 125, 0.16);
            border: 1px solid var(--c-sage);
        }

        .status-message.error {
            background: rgba(138, 118, 80, 0.12);
            border: 1px solid rgba(138, 118, 80, 0.65);
            margin-bottom: 14px;
        }

        .status-message p {
            margin: 0;
        }

        .status-message p + p {
            margin-top: 6px;
        }

        /* overlay-close anchor covers background and is clickable */
        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        /* full-screen invisible link behind the popup container */
        .popup-overlay .overlay-close {
            position: absolute;
            inset: 0;
            display: block;
            z-index: 1;
            /* behind popup-container */
            background: transparent;
            /* transparent but clickable */
            text-indent: -9999px;
            /* hide text for accessibility if any */
        }

        /* popup container sits above the overlay-close */
        .popup-container {
            position: relative;
            z-index: 2;
            background: var(--c-linen);
            padding: 24px 26px;
            max-width: 440px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        /* CSS (place near the other card/grid styles) */
        .stat-grid .inline-form {
            height: 100%;
            display: block;
        }

        .stat-grid .inline-form .card {
            height: 100%;
        }
    

        /* Mobile */
        @media (max-width: 480px) {
            .popup-container {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .card {
                min-height: 100px;
                padding: 1rem;
            }

            .card-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <nav class="main-navbar">
            <div class="brand-group">
                <div class="logo-frame">
                    <img src="../../assets/images/logo3.png" alt="Logo">
                </div>
                <h1 class="brand-name">Admin Dashboard</h1>
            </div>

            <div class="nav-actions">
                <h4 class="nav-username">ADMIN</h4>
                <div class="avatar-frame">
                    <img src="../../assets/images/profile3.png" alt="Profile">
                </div>
                <form method="post">
                    <?php echo csrf_input(); ?>
                    <button type="submit" class="exit-btn" name="logout_submit">Logout</button>
                </form>
            </div>
        </nav>

        <main class="content">
            <?php if ($successMessage !== ''): ?>
                <p class="status-message success"><?php echo e($successMessage); ?></p>
            <?php endif; ?>

            <section class="stat-grid">
                <form method="post" class="inline-form">
                    <?php echo csrf_input(); ?>
                    <button type="submit" name="open_add_popup" class="card add-card card-button" aria-label="Add books">
                        <span class="plus-icon" aria-hidden="true"></span>
                        <span class="card-label">Add books</span>
                    </button>
                </form>
                <div class="card">
                    <p class="card-number">
                        <?php echo $totalBooks; ?>
                    </p>
                    <p class="card-label">Total books</p>
                </div>
                <div class="card">
                    <p class="card-number">
                        <?php echo $totalCategories; ?>
                    </p>
                    <p class="card-label">Categories</p>
                </div>
                <a href="../issue/issue.php" style="text-decoration:none;">
                    <div class="card">
                        <form method="post" class="inline-form">
                            <?php echo csrf_input(); ?>
                            <button type="button" class="card-button" aria-label="issue">
                                <p class="card-number">
                                    <?php echo $totalIssuedBooks; ?>
                                </p>
                                <p class="card-label">Issue</p>
                            </button>
                        </form>
                    </div>
                </a>
            </section>
            <br>
            <section class="list-section">
                <div class="list-header">
                    <p class="intro">Welcome to the Library. Here you can find resources, books, and study materials.</p>
                    <form class="search-form" method="post">
                        <?php echo csrf_input(); ?>
                        <input type="text" name="search" placeholder="Search books by name..." class="searchInput" />
                        <button type="submit" class="primary-btn" name="search_submit">Submit</button>
                    </form>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="table-head" id="B-id">Book ID</th>
                                <th class="table-head" id="B-name">Book Name</th>
                                <th class="table-head" id="B-author">Author</th>
                                <th class="table-head" id="B-category">Category</th>
                                <th class="table-head" id="B-pdf">PDF Link</th>
                                <th class="table-head" id="B-delete">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td class="table-body" id="B-<?php echo $book['id']; ?>"><?php echo $book['id']; ?></td>
                                    <td class="table-body" id="B-name-<?php echo $book['id']; ?>"><?php echo $book['name']; ?></td>
                                    <td class="table-body" id="B-author-<?php echo $book['id']; ?>"><?php echo $book['author']; ?></td>
                                    <td class="table-body" id="B-category-<?php echo $book['id']; ?>"><?php echo $book['category']; ?></td>
                                    <td class="table-body" id="B-pdf-<?php echo $book['id']; ?>">
                                        <?php $pdfHref = book_pdf_href($book['pdf_path'] ?? '', $appBaseUrl); ?>
                                        <?php if ($pdfHref !== ''): ?>
                                            <a href="<?php echo e($pdfHref); ?>" target="_blank" rel="noopener">View PDF</a>
                                        <?php else: ?>
                                            NA
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-body" id="BB-delete-<?php echo $book['id']; ?>">
                                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            <?php echo csrf_input(); ?>
                                            <input type="hidden" name="delete_book_id" value="<?php echo $book['id']; ?>">
                                            <button type="submit" name="delete_book_submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>


    <?php if ($showPopup): ?>
        <div class="popup-overlay">
            <a class="overlay-close" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">Close popup</a>

            <div class="popup-container">
                <form class="popup-form" method="post" enctype="multipart/form-data">
                    <?php echo csrf_input(); ?>
                    <?php if ($popupErrors !== []): ?>
                        <div class="status-message error">
                            <?php foreach ($popupErrors as $popupError): ?>
                                <p><?php echo e($popupError); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <label class="form-label">Name</label>
                    <input class="text-input" type="text" name="name" required>

                    <label class="form-label">Category</label>
                    <input class="text-input" type="text" name="category" required>

                    <label class="form-label">Author</label>
                    <input class="text-input" type="text" name="author" required>

                    <label class="form-label">Description</label>
                    <textarea class="text-input" name="description" rows="5" placeholder="Write a short description of the book..." required></textarea>

                    <label class="file-box" for="image" data-placeholder="Click to upload book image">
                        Click to upload book image
                    </label>
                    <input class="file-input" type="file" name="image" id="image" accept="image/*" required>
                
                    <label class="file-box" for="pdf" data-placeholder="Click to upload book PDF">
                        Click to upload book PDF
                    </label>
                    <input class="file-input" type="file" name="pdf" id="pdf" accept="application/pdf" required>

                    <button class="submit-btn" type="submit" name="add_book_submit">Submit</button>

                </form>
            </div>

        </div>
    <?php endif; ?>
</body>
<script>
const imageInput = document.getElementById("image");
const pdfInput = document.getElementById("pdf");

const imageBox = document.querySelector('.file-box[for="image"]');
const pdfBox = document.querySelector('.file-box[for="pdf"]');

imageInput.addEventListener("change", function(){
    const file = this.files[0];
    if(!file) return;

    const url = URL.createObjectURL(file);

    imageBox.innerHTML = `
        <img src="${url}" style="width:80px;height:80px;object-fit:contain;">
        <button type="button" onclick="removeImage()" class="file-remove-btn">×</button>
    `;
});

function removeImage(){
    imageInput.value="";
    imageBox.innerHTML="Click to upload book image";
}

pdfInput.addEventListener("change", function(){
    const file = this.files[0];
    if(!file) return;

    pdfBox.innerHTML = `
        ${file.name}<br>
        ${(file.size/1024).toFixed(1)} KB
        <button type="button" onclick="removePdf()" class="file-remove-btn">×</button>
    `;
});

function removePdf(){
    pdfInput.value="";
    pdfBox.innerHTML="Click to upload book PDF";
}
</script>
</html>
