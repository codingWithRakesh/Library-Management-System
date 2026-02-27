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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management System</title>
    
</head>
<body>
    <h2>Book Management System</h2>
    <form method="post">
        <input type="text" name="bookId" placeholder="Enter Book ID">
        <input type="text" name="studentId" placeholder="Enter Student ID">
        <input type="submit" value="View Book Details">
    </form>
</body>
</html>