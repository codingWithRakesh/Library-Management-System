<?php
    session_start();
    include "../db/db.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../student/login/login.php");
        exit();
    }

    $checkUserByIdSql = "SELECT id FROM students WHERE id = " . intval($_SESSION['user_id']);
    $checkResult = mysqli_query($conn, $checkUserByIdSql);
    if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
        session_destroy();
        header("Location: ../login/login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];

    $bookId = $_GET['id'] ?? '';
    $category = $_GET['category'] ?? '';

    $fetchBookSql = "SELECT * FROM books WHERE id='$bookId'";
    $bookResult = mysqli_query($conn, $fetchBookSql);
    if ($bookResult->num_rows > 0) {
        $book = mysqli_fetch_assoc($bookResult);
    } else {
        echo "Book not found.";
        exit();
    }

    $checkBorrowedSql = "SELECT * FROM issued_books WHERE book_id='$bookId' AND student_id='$userId' AND return_date >= CURDATE()";
    $borrowedResult = mysqli_query($conn, $checkBorrowedSql);
    $isBorrowed = $borrowedResult && $borrowedResult->num_rows > 0;

    $fetchBookByCategorySql = "SELECT * FROM books WHERE category='$category' AND id != '$bookId' LIMIT 4";
    $booksByCategoryResult = mysqli_query($conn, $fetchBookByCategorySql);
    if ($booksByCategoryResult->num_rows > 0) {
        $suggestedBooks = [];
        while ($row = mysqli_fetch_assoc($booksByCategoryResult)) {
            $isUrlSuggested = $row["isUrl"] ?? false;
            $suggestedBooks[] = [
                "id" => $row['id'],
                "title" => $row['name'] ?? 'Unknown Title',
                "author" => $row['author'] ?? 'Unknown Author',
                "category" => $row['category'] ?? 'Unknown Category',
                "image" => $isUrlSuggested ? $row['image_path'] : (!empty($row['image_path']) ? "../assets/images/" . $row['image_path'] : "https://covers.openlibrary.org/b/id/8259449-L.jpg")
            ];
        }
    } else {
        $suggestedBooks = [];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["borrow_book"])) {
        $bookId = $_POST["bookId"] ?? '';
        $studentId = $_SESSION['user_id'];

        if(empty($bookId) || empty($studentId)) {
            echo "Book ID and Student ID are required.";
            exit();
        }

        $sql = "SELECT * FROM books WHERE id='$bookId'";
        $result = mysqli_query($conn, $sql);

        if ($result->num_rows > 0) {

            $issueTableSql = "CREATE TABLE IF NOT EXISTS issued_books (
                id INT AUTO_INCREMENT PRIMARY KEY,
                book_id INT NOT NULL,
                student_id INT NOT NULL,
                issue_date DATE NOT NULL,
                return_date DATE NULL,
                FOREIGN KEY (book_id) REFERENCES books(id),
                FOREIGN KEY (student_id) REFERENCES students(id)
            )";
            if (!mysqli_query($conn, $issueTableSql)) {
                echo "Error creating issued_books table: " . mysqli_error($conn);
                exit();
            }

            $sqlAssign = "INSERT INTO issued_books (book_id, student_id, issue_date) VALUES ('$bookId', '$studentId', CURDATE())";
            if (mysqli_query($conn, $sqlAssign)) {
                $returnDate = date('Y-m-d', strtotime('+7 days'));
                $sqlUpdate = "UPDATE issued_books SET return_date = '$returnDate' WHERE book_id = '$bookId' AND student_id = '$studentId' ORDER BY id DESC LIMIT 1";
                mysqli_query($conn, $sqlUpdate);
                $isBorrowed = true;
                echo "Book issued successfully. Return date: " . $returnDate;
                header("Refresh: 2; url=" . $_SERVER['REQUEST_URI']);
            } else {
                echo "Error issuing book: " . mysqli_error($conn);
            }
        } else {
            echo "No book found with the given ID.";
        }
    }
?>

<?php
$dbImage = $book["image_path"] ?? "";
$isUrl = $book["isUrl"] ?? false;
$mainBook = [
    "title" => $book['name'] ?? 'Unknown Title',
    "author" => $book['author'] ?? 'Unknown Author',
    "description" => $book['discription'] ?? "A mesmerizing tale of a young boy's quest to protect a mysterious book in post-war Barcelona. Hidden deep in the city is the Cemetery of Forgotten Books, a library of obscure and forgotten titles. It is a story weaving romance, mystery, and magic.",
    "image" => $isUrl ? $dbImage : (!empty($dbImage) ? "../assets/images/" . $dbImage : "https://covers.openlibrary.org/b/id/8259449-L.jpg"),
    "category" => $book['category'] ?? 'Unknown Category',
    "pdf_link" => $book['pdf_path'] ? "../assets/pdfs/" . $book['pdf_path'] : ""
];

// Mock User Data
$currentUser = [
    "name" => "Ethan Clarke",
    "photo" => "../assets/images/default.jpeg" 
];
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../student/login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title><?php echo $mainBook['title']; ?> - Book Details</title>
    <style>
        /* CSS Color Variables from your palette */
        :root {
            --dark-brown: #8a7650;
            --sage-green: #8e977d;
            --light-cream: #ece7d1;
            --beige: #dbcea5;
        }

        /* Basic Reset & Typography */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat','Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-cream);
            color: var(--dark-brown);
            line-height: 1.6;
            padding: 100px 20px 40px 20px; 
        }

        /* --- Fixed Header Section --- */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: var(--beige);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 40px;
           border-bottom: 1px solid rgba(138, 118, 80, 0.2);
            z-index: 1000;
        }

        .header-left, .header-right {
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .logoContainer{
            width: 16.7rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            /* border: 2px solid black; */
        }

        .logoContainer a{
            text-decoration: none;
        }

        .logo{
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-link{
            color: #8a7650;
            font-weight: bold;
            font-size: 14px;
        }

        .site-logo {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .company-name {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--dark-brown);
            letter-spacing: 2px;
        }

        .user-photo {
            height: 45px;
            width: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--sage-green);
        }

        .user-name {
            font-weight: 600;
            font-size: 1rem;
            color: var(--dark-brown);
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* --- Main Book Section --- */
        .book-details {
            display: flex;
            background-color: var(--beige);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(138, 118, 80, 0.2);
            gap: 40px;
            margin-bottom: 50px;
        }

        .book-image img {
            width: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .book-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .book-info h1 {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }

        .book-info h3 {
            color: var(--sage-green);
            font-weight: 500;
            margin-bottom: 20px;
            font-style: italic;
        }

        .book-info p {
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        /* Buttons */
        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none; 
            display: inline-block;
            text-align: center;
        }

        .btn:active {
            transform: translateY(2px);
        }

        .btn-borrow {
            background-color: var(--sage-green);
            color: var(--light-cream);
        }

        .btn-borrow:hover {
            opacity: 0.85;
        }

        /* PDF Button Style */
        .btn-pdf {
            background-color: transparent;
            color: var(--dark-brown);
            border: 2px solid var(--dark-brown);
            padding: 10px 23px; 
        }

        .btn-pdf:hover {
            background-color: var(--dark-brown);
            color: var(--light-cream);
        }

        /* --- Suggested Books Section --- */
        .suggestions-title {
            font-size: 1.8rem;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--beige);
            padding-bottom: 10px;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .suggestion-card {
            background-color: var(--beige);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 8px rgba(138, 118, 80, 0.1);
        }

        .suggestion-card:hover {
            transform: translateY(-5px);
        }

        .suggestion-card img {
            width: 100%;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .suggestion-card h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .suggestion-card p {
            font-size: 0.9rem;
            color: var(--sage-green);
        }

        .user-info{
            height: 35px;
            width: 35px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }
        
        .user-info img { width: 100%; height: 100%; object-fit: cover; }

        .modal-logout{
            position: fixed;
            top: 60px;
            right: 20px;
            background: #fff;
            border: 1px solid #8a7650;
            width: 10rem;
            display: none;
            flex-direction: column;
            padding: 1rem;
            gap: 10px;
            border-radius: 4px;
        }

        .btn-logout {
            background: #8a7650;
            color: white;
            border: none;
            padding: 8px 0;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .site-header {
                padding: 0 20px;
            }
            .company-name {
                display: none; 
            }
            .book-details {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .actions {
                justify-content: center;
            }
        }
    </style>
    <title>Book Management System</title>
    
</head>
<body>

<header class="site-header">
    <div class="header-left logoContainer" >
        <a class="logo" href="../index.php">
            <img src="../assets/images/logo3.png" alt="NeonLeaf Logo" style="width: 40px; height: 40px;">
            <span class="company-name">NeonLeaf</span>
        </a>
        <a class="nav-link" href="../student/issue/issue.php">My Books <i class="fa-solid fa-caret-down"></i></a>
    </div>
    <div id="user-info" class="user-info">
        <img src="../assets/images/default.jpeg" alt="User Icon">
    </div>
</header>

<div id="modal-logout" class="modal-logout">
    Hello <?php echo $userName; ?>
    <form method="post">
        <button class="btn-logout" name="logout">Logout</button>
    </form>
</div>

<script>
    let isOn = false;
    const userInfo = document.getElementById('user-info');
    const modalLogout = document.getElementById('modal-logout');

    userInfo.addEventListener('click', () => {
        isOn = !isOn;
        modalLogout.style.display = isOn ? 'flex' : 'none';
    });
</script>

<div class="container">
    <div class="book-details">
        <div class="book-image">
            <img src="<?php echo $mainBook['image']; ?>" alt="Cover of <?php echo $mainBook['title']; ?>">
        </div>
        <div class="book-info">
            <h1><?php echo $mainBook['title']; ?></h1>
            <h3>By <?php echo $mainBook['author']; ?></h3>
            <p><?php echo $mainBook['description']; ?></p>
            
            <div class="actions">
                <form method="post">
                    <input type="hidden" name="bookId" value="<?php echo $bookId; ?>">
                    <button class="btn btn-borrow" name="borrow_book" <?php echo $isBorrowed ? 'disabled' : ''; ?>><?php echo $isBorrowed ? 'Borrowed' : 'Borrow Book'; ?></button>
                </form>
                <?php if (!empty($mainBook['pdf_link'])): ?>
                    <a href="<?php echo htmlspecialchars($mainBook['pdf_link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn btn-pdf">
                        View PDF
                    </a>
                <?php else: ?>
                    <span style="color: var(--dark-brown); font-style: italic;">PDF not available</span>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <h2 class="suggestions-title">More in <?php echo $mainBook['category']; ?></h2>
    <div class="suggestions-grid">
        <?php foreach ($suggestedBooks as $book): ?>
            <a class="suggestion-card" href="../book/book.php?id=<?php echo urlencode($book['id']); ?>&&category=<?php echo urlencode($book['category']); ?>" style="text-decoration: none; color: inherit;">
                <img src="<?php echo $book['image']; ?>" alt="Cover of <?php echo $book['title']; ?>">
                <h4><?php echo $book['title']; ?></h4>
                <p><?php echo $book['author']; ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>