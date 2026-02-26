<?php
    include "../../db/db.php";

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
            WHERE ib.student_id = 1";

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
            WHERE ib.student_id = 1 AND b.name LIKE '%$searchTerm%'";
        
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Book Management System</title>
    <link rel="icon" href="../../assets/images/logo3.png" type="image/png">

</head>
<body>
    <h2>Issue Book Management System</h2>
</body>
</html><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Library — Books List for user</title>

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


        /* navbar: copied from previous page */
        .main-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            background: var(--c-brown);
            color: var(--c-linen);
            padding: 1rem 1.5rem;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 10px 24px rgba(43, 36, 24, 0.18);
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

    <div class="page">

        <!-- NAVBAR (exact from previous) -->
        <nav class="main-navbar">
            <div class="brand-group">
                <div class="logo-frame">
                    <!-- replace src with your logo path -->
                    <img src="../../assets/images/logo.png" alt="Logo">
                </div>
                <h1 class="brand-name">Dashboard</h1>
            </div>

            <div class="nav-actions">
                <h4 class="nav-username">CYRUS</h4>
                <div class="avatar-frame">
                    <img src="../../assets/images/profile3.png" alt="Profile">
                </div>
                <button class="exit-btn" type="button">Exit</button>
            </div>
        </nav>

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