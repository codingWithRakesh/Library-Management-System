<?php
    session_start();
    include "../../db/db.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login/login.php");
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

    $tableSql = "CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        category VARCHAR(255) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        pdf_path VARCHAR(255) NULL
    )";

    $issueTableSql = "CREATE TABLE IF NOT EXISTS issued_books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        book_id INT NOT NULL,
        student_id INT NOT NULL,
        issue_date DATE NOT NULL,
        return_date DATE NULL,
        FOREIGN KEY (book_id) REFERENCES books(id),
        FOREIGN KEY (student_id) REFERENCES students(id)
    )";

    $fineTableSql = "CREATE TABLE IF NOT EXISTS fines (
        id INT AUTO_INCREMENT PRIMARY KEY,
        issued_book_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (issued_book_id) REFERENCES issued_books(id)
    )";

    if (!mysqli_query($conn, $tableSql)) {
        echo "Error creating books table: " . mysqli_error($conn);
        exit();
    }
    if (!mysqli_query($conn, $issueTableSql)) {
        echo "Error creating issued_books table: " . mysqli_error($conn);
        exit();
    }
    if (!mysqli_query($conn, $fineTableSql)) {
        echo "Error creating fines table: " . mysqli_error($conn);
        exit();
    }

    $joinSql = "SELECT ib.id AS issue_id,
            b.id AS book_id,
            b.name AS book_name,
            b.category,
            ib.issue_date,
            ib.return_date,
            f.amount AS fine_amount
            FROM issued_books ib
            JOIN books b ON ib.book_id = b.id
            LEFT JOIN fines f ON ib.id = f.issued_book_id
            WHERE ib.student_id = $userId";

    $result = mysqli_query($conn, $joinSql);
    if (!$result) {
        echo "Error fetching issued books: " . mysqli_error($conn);
        exit();
    }
?>

<?php
    if(isset($_POST['search'])) {
        $searchTerm = mysqli_real_escape_string($conn, $_POST['q']);
        $searchSql = "SELECT ib.id AS issue_id,
            b.id AS book_id,
            b.name AS book_name,
            b.category,
            ib.issue_date,
            ib.return_date,
            f.amount AS fine_amount
            FROM issued_books ib
            JOIN books b ON ib.book_id = b.id
            LEFT JOIN fines f ON ib.id = f.issued_book_id
            WHERE ib.student_id = $userId AND b.name LIKE '%$searchTerm%'";
        
        $result = mysqli_query($conn, $searchSql);
        if (!$result) {
            echo "Error searching issued books: " . mysqli_error($conn);
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Issue Book Management System</title>
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
            font-family: 'Manrope', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(180deg, #f4efde 0%, #ece7d1 55%, #f5f0dd 100%);
            color: var(--c-ink);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }


       

        .brand-group {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .logo-frame {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 2px solid var(--c-linen);
            background: var(--c-sand);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .logo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .brand-name {
            margin: 0;
            font-size: 1.25rem;
            letter-spacing: .04em;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .nav-username {
            margin: 0;
            font-weight: 700;
            letter-spacing: .08em;
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
            display: block;
        }

        .exit-btn {
            background: transparent;
            color: var(--c-linen);
            border: 2px solid var(--c-linen);
            padding: .45rem .75rem;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
        }

        .exit-btn:hover {
            background: var(--c-linen);
            color: var(--c-brown);
        }

        /* page header area */
        .page-header {
            padding: 0 1.5rem;
            margin: 1.25rem 0 .6rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.1rem;
            margin: 0;
            font-weight: 700;
            color: #42392d;
        }

        /* search form */
        .search-form {
            display: flex;
            gap: .75rem;
            align-items: center;
        }

        .searchInput {
            padding: .7rem 1rem;
            border-radius: 10px;
            border: 2px solid var(--c-sand);
            background: #f8f3e3;
            min-width: 260px;
            outline: none;
        }

        .searchInput:focus {
            border-color: var(--c-brown);
            box-shadow: 0 0 0 4px rgba(138, 118, 80, 0.08);
        }

        .primary-btn {
            background: var(--c-sage);
            color: #fff;
            border: none;
            padding: .7rem 1rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .primary-btn:hover {
            background: #7e8a6f;
        }

        /* table area */
        .table-wrap {
            margin-top: 1rem;
            background: var(--c-white);
            border: 2px solid var(--c-sand);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 12px 26px rgba(43, 36, 24, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
            background: transparent;
        }

        thead th {
            background: var(--c-sage);
            color: var(--c-white);
            text-align: left;
            padding: .85rem;
            font-weight: 700;
            letter-spacing: .03em;
        }

        tbody td {
            padding: .85rem;
            border-bottom: 1px solid rgba(219, 206, 165, 0.6);
            color: #3d3529;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background: rgba(236, 231, 209, 0.45);
        }

        tbody tr:hover {
            background: rgba(142, 151, 125, 0.06);
        }

        .table-body a {
            color: var(--c-brown);
            font-weight: 700;
            text-decoration: none;
        }

        .table-body button {
            background: transparent;
            border: 1.4px solid var(--c-brown);
            color: var(--c-brown);
            padding: .35rem .65rem;
            border-radius: 8px;
            cursor: pointer;
        }

        .table-body button:hover {
            background: var(--c-brown);
            color: var(--c-linen);
        }

         header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 40px;
            background-color: var(--header-bg);
            border-bottom: 1px solid rgba(138, 118, 80, 0.2); 
        }

        .logo {
            font-family: "Times New Roman", serif;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--secondary-color);
        }
        
        .logo span { background: var(--secondary-color); color: #fff; padding: 0 5px; border-radius: 2px; }

        .nav-links {
            display: flex;
            gap: 20px;
            font-size: 14px;
            margin-right: auto;
            margin-left: 30px;
            color: var(--secondary-color);
            font-weight: bold;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid var(--secondary-color);
            border-radius: 3px;
            overflow: hidden;
            width: 400px;
            margin: 0 20px;
        }

        .search-select {
            background: #fdfdfd;
            border: none;
            padding: 8px;
            border-right: 1px solid #ccc;
            font-size: 13px;
            color: #555;
            outline: none;
        }

        .search-input {
            border: none;
            padding: 8px;
            flex-grow: 1;
            outline: none;
        }

        .search-btn {
            background: none;
            border: none;
            outline: none;
            padding: 0;
        }

        .search-icon {
            padding: 8px 12px;
            color: var(--secondary-color);
            cursor: pointer;
        }
        
        .search-icon:hover { background: #eee; }


        /* responsive tweaks */
        @media (max-width:980px) {
            .table-wrap {
                padding: .75rem;
            }

            table {
                min-width: 720px;
            }
        }

        @media (max-width:640px) {
            .brand-name {
                font-size: 1rem;
            }

            .searchInput {
                min-width: 140px;
                flex: 1 1 auto;
            }
        }
    </style>
</head>

<body>

<header>
        <div class="logo">
            <i class="fa-solid fa-book-open" style="margin-right:5px; color: var(--secondary-color);"></i>
            LIBRARY
        </div>
        
      

        <form class="search-bar" method="GET" action="?">
            <select name="category_filter" class="search-select">
                <option>All</option>
                <option>Category</option>
                <option>Author</option>
            </select>
            <input type="text" name="search" class="search-input" placeholder="Search (e.g., 'Python' or 'Science')" value="<?php echo htmlspecialchars($searchQuery); ?>" required>
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
            </button>
        </form>
    </header>


        <!-- header + search -->
        <div class="page-header">
            <p class="page-title">Welcome to the Library. Browse the collection below or use search.</p>

            <form class="search-form" method="post" >
                <input class="searchInput" name="q" type="text" placeholder="Search books by name..." />
                <button class="primary-btn" type="submit" name="search">Submit</button>
            </form>
        </div>

        <!-- TABLE -->
        <div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th class="table-head">Serial no.</th>
        <th class="table-head">Book ID</th>
        <th class="table-head">Book Name</th>
        <th class="table-head">Category</th>
        <th class="table-head">Issue Date</th>
        <th class="table-head">Last Date</th>
        <th class="table-head">Fine</th>
      </tr>
    </thead>

    <tbody>
        <?php
            $serialNo = 1;
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
      <tr>
        <td class="table-body"><?php echo $serialNo++; ?></td>
        <td class="table-body"><?php echo $row['book_id']; ?></td>
        <td class="table-body"><?php echo $row['book_name']; ?></td>
        <td class="table-body"><?php echo $row['category']; ?></td>
        
        <td class="table-body"><?php echo $row['issue_date']; ?></td>
        <td class="table-body"><?php echo $row['return_date']; ?></td>
        <td class="table-body">
            <?php 
                echo $row['fine_amount'] ? $row['fine_amount'] : "Not Applicable";
            ?>
        </td>
     </tr>
     <?php
        }
     ?>
      
    </tbody>
  </table>
</div>

    </div>
</body>

</html>