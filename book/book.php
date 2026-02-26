<?php
    include "../db/db.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $bookId = $_POST["bookId"] ?? '';
        $studentId = $_POST["studentId"] ?? '';

        if(empty($bookId) || empty($studentId)) {
            echo "Book ID and Student ID are required.";
            exit();
        }

        $sql = "SELECT * FROM books WHERE id='$bookId'";
        $result = mysqli_query($conn, $sql);

        if ($result->num_rows > 0) {
            $book = mysqli_fetch_assoc($result);
            echo "<h3>Book Details:</h3>";
            echo "Name: " . $book["name"] . "<br>";
            echo "Author: " . $book["author"] . "<br>";
            echo "Category: " . $book["category"] . "<br>";
            if (!empty($book["pdf_path"])) {
                echo "<a href='" . $book["pdf_path"] . "' target='_blank'>View PDF</a><br>";
            }
            if (!empty($book["image_path"])) {
                echo "<img src='" . $book["image_path"] . "' alt='Book Image' style='max-width:200px;'><br>";
            }

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
                echo "Book issued successfully. Return date: " . $returnDate;
            } else {
                echo "Error issuing book: " . mysqli_error($conn);
            }
        } else {
            echo "No book found with the given ID.";
        }
    }
?>

<?php
// Mock Data: Added "pdf_link" to simulate a path to your PDF file
$mainBook = [
    "title" => "The Shadow of the Wind",
    "author" => "Carlos Ruiz Zafón",
    "description" => "A mesmerizing tale of a young boy's quest to protect a mysterious book in post-war Barcelona. Hidden deep in the city is the Cemetery of Forgotten Books, a library of obscure and forgotten titles. It is a story weaving romance, mystery, and magic.",
    "image" => "https://covers.openlibrary.org/b/id/8259449-L.jpg",
    "category" => "Historical Mystery",
    "pdf_link" => "sample_book.pdf" // <-- Point this to your actual PDF file path
];

// Mock Data for suggestions based on the same category
$suggestedBooks = [
    [
        "title" => "The Name of the Rose", 
        "author" => "Umberto Eco", 
        "image" => "https://covers.openlibrary.org/b/id/8259449-L.jpg"
    ],
    [
        "title" => "The Thirteenth Tale", 
        "author" => "Diane Setterfield", 
        "image" => "https://covers.openlibrary.org/b/id/8259449-L.jpg"
    ],
    [
        "title" => "The Book Thief", 
        "author" => "Markus Zusak", 
        "image" => "https://covers.openlibrary.org/b/id/8259449-L.jpg"
    ],
    [
        "title" => "Foucault's Pendulum", 
        "author" => "Umberto Eco", 
        "image" => "https://covers.openlibrary.org/b/id/8259449-L.jpg"
    ]
];

// Mock User Data
$currentUser = [
    "name" => "Ethan Clarke",
    "photo" => "WhatsApp Image.jpg" 
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            height: 70px;
            background-color: var(--beige);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            box-shadow: 0 2px 10px rgba(138, 118, 80, 0.2);
            z-index: 1000;
        }

        .header-left, .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
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
            text-transform: uppercase;
            letter-spacing: 1px;
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
</head>
<body>

<header class="site-header">
    <div class="header-left">
        <img src="cropped_circle_image.png" alt="Library Logo" class="site-logo">
        <span class="company-name">Harvest Learning</span>
    </div>
    <div class="header-right">
        <span class="user-name"><?php echo $currentUser['name']; ?></span>
        <img src="<?php echo $currentUser['photo']; ?>" alt="User Profile" class="user-photo">
    </div>
</header>

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
                <button class="btn btn-borrow">Borrow Book</button>
                <a href="<?php echo $mainBook['pdf_link']; ?>" target="_blank" class="btn btn-pdf">Read PDF</a>
            </div>
        </div>
    </div>

    <h2 class="suggestions-title">More in <?php echo $mainBook['category']; ?></h2>
    <div class="suggestions-grid">
        <?php foreach ($suggestedBooks as $book): ?>
            <div class="suggestion-card">
                <img src="<?php echo $book['image']; ?>" alt="Cover of <?php echo $book['title']; ?>">
                <h4><?php echo $book['title']; ?></h4>
                <p><?php echo $book['author']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>