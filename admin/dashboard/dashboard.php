<?php
    include "../../db/db.php";
?>

<?php
    $tableSql = "CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        category VARCHAR(255) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        pdf_path VARCHAR(255) NULL
    )";

    if (!mysqli_query($conn, $tableSql)) {
        echo "Error creating table: " . mysqli_error($conn);
        exit();
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
?>

<?php
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
        } else {
            $books = [];
        }
    }

    //delete book functionality
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book_submit'])) {
        $deleteBookId = $_POST['delete_book_id'] ?? '';
        if (!empty($deleteBookId)) {
            $deleteSql = "DELETE FROM books WHERE id='$deleteBookId'";
            mysqli_query($conn, $deleteSql);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
?>

<?php
    if (isset($_GET['success'])) {
        echo "<script>alert('Book added successfully');</script>";
    }
?>

<?php
$showPopup = false;

// open popup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['open_add_popup'])) {
    $showPopup = true;
}

// add book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book_submit'])) {
    $name = $_POST["name"] ?? '';
    $author = $_POST["author"] ?? '';
    $category = $_POST["category"] ?? '';

    if(empty($name) || empty($author) || empty($category)) {
        echo "All fields are required.";
        exit();
    }

    $tableSql = "CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        category VARCHAR(255) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        pdf_path VARCHAR(255) NULL
    )";

    if (!mysqli_query($conn, $tableSql)) {
        echo "Error creating table: " . mysqli_error($conn);
        exit();
    }
    if(empty($_FILES["image"]["name"])) {
        echo "Image file is required.";
        exit();
    }
    $imageFileName = $_FILES["image"]["name"] ?? '';
    $imageTmpName = $_FILES["image"]["tmp_name"] ?? '';
    $targetImagePath = "../../assets/images/" . basename($imageFileName);

    $pdfFileName = $_FILES["pdf"]["name"] ?? '';
    $pdfTmpName = $_FILES["pdf"]["tmp_name"] ?? '';
    $targetPdfPath = "";

    if(!empty($pdfFileName)){
        $targetPdfPath = "../../assets/pdfs/" . basename($pdfFileName);
    }

    if(move_uploaded_file($imageTmpName, $targetImagePath) && ( empty($pdfFileName) || move_uploaded_file($pdfTmpName, $targetPdfPath) )) {
        $insertSql = "INSERT INTO books (name, author, category, image_path, pdf_path) VALUES ('$name', '$author', '$category', '$targetImagePath', '$targetPdfPath')";
        if (mysqli_query($conn, $insertSql)) {
            header("Location: ".$_SERVER['PHP_SELF']."?success=1");
            exit();
        } else {
            echo "Error: " . $insertSql . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Error uploading files.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            background: var(--c-brown);
            color: var(--c-linen);
            padding: 1rem 1.5rem;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 10px 24px rgba(43, 36, 24, 0.18);
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
            color: var(--c-linen);
            border: 2px solid var(--c-linen);
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
            padding-top: 2rem;
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
        }

        .file-box:hover {
            background-color: #d7c89a;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            margin-top: 14px;
            /* clear separation */
            padding: 12px;
            background-color: var(--c-brown);
            color: var(--c-linen);
            border: none;
            border-radius: 7px;
            font-size: 15px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: var(--c-sage);
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
                    <img src="../../assets/images/OOP.jpeg" alt="Logo">
                </div>
                <h1 class="brand-name">Admin Dashboard</h1>
            </div>

            <div class="nav-actions">
                <h4 class="nav-username">CYRUS</h4>
                <div class="avatar-frame">
                    <img src="../../assets/images/profile3.png" alt="Profile">
                </div>
                <button class="exit-btn" type="button">Logout</button>
            </div>
        </nav>

        <main class="content">
            <section class="stat-grid">
                <form method="post" class="inline-form" style="display:inline-block;">
                    <button type="submit" name="open_add_popup" class="card add-card card-button" aria-label="Add books">
                        <span class="plus-icon" aria-hidden="true"></span>
                        <span class="card-label">Add books btn</span>
                    </button>
                </form>
                <div class="card">
                    <p class="card-number">
                        <?php echo $totalBooks; ?>
                    </p>
                    <p class="card-label">total books</p>
                </div>
                <div class="card">
                    <p class="card-number">
                        <?php echo $totalCategories; ?>
                    </p>
                    <p class="card-label">categories</p>
                </div>
                <div class="card">
                    <p class="card-number">10</p>
                    <p class="card-label">users</p>
                </div>
            </section>
            <br>
            <section class="list-section">
                <div class="list-header">
                    <p class="intro">Welcome to the Library. Here you can find resources, books, and study materials.</p>
                    <form class="search-form" method="post">
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
                                        <?php if (!empty($book['pdf_path'])): ?>
                                            <a href="<?php echo $book['pdf_path']; ?>" target="_blank">View PDF</a>
                                        <?php else: ?>
                                            NA
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-body" id="BB-delete-<?php echo $book['id']; ?>">
                                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this book?');">
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

        <label class="form-label">Name</label>
        <input class="text-input" type="text" name="name" required>

        <label class="form-label">Category</label>
        <input class="text-input" type="text" name="category" required>

        <label class="form-label">Author</label>
        <input class="text-input" type="text" name="author" required>

        <label class="file-box" for="image">Click to upload book image</label>
        <input class="file-input" type="file" name="image" id="image">

        <label class="file-box" for="pdf">Click to upload book PDF</label>
        <input class="file-input" type="file" name="pdf" id="pdf">

        <button class="submit-btn" type="submit" name="add_book_submit">Submit</button>

      </form>
    </div>

  </div>
<?php endif; ?>
</body>

</html>