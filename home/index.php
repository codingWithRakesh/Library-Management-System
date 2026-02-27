<?php
    include "../db/db.php";

    $allbooksSql = "SELECT * FROM books";
    $allBooksResult = mysqli_query($conn, $allbooksSql);
    
    $allBooks = []; 
    $groupedBooks = [];

    if ($allBooksResult && $allBooksResult->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($allBooksResult)) {
            $dbImage = $row["image_path"] ?? "";
            $finalImageUrl = !empty($dbImage) ? "../assets/images/" . $dbImage : "https://placehold.co/150x220/8e977d/ffffff?text=No+Image";

            $allBooks[] = [
                "id" => $row["id"],
                "title" => $row["name"],
                "author" => $row["author"],
                "category" => $row["category"],
                "image" => $finalImageUrl,
                "status" => "Borrow",
                "btn_class" => "btn-primary"
            ];
        }
    } 

    foreach ($allBooks as $book) {
        $groupedBooks[$book['category']][] = $book;
    }
    
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
    $searchResults = [];
    $showModal = false;

    if (!empty($searchQuery)) {
        $showModal = true;
        foreach ($allBooks as $book) {
            if (stripos($book['title'], $searchQuery) !== false || 
                stripos($book['author'], $searchQuery) !== false || 
                stripos($book['category'], $searchQuery) !== false) {
                $searchResults[] = $book;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Catalog</title>
    <link rel="icon" href="../../assets/images/logo3.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Applied ColorHunt Palette: 8a7650 | 8e977d | ece7d1 | dbcea5 */
            --bg-color: #ece7d1;
            --header-bg: #dbcea5;
            --primary-color: #8e977d; /* Sage Green */
            --secondary-color: #8a7650; /* Dark Brown */
            
            --text-dark: #333;
            --card-bg: #fff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        a { text-decoration: none; color: inherit; }

        /* HEADER STYLES */
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

        /* MAIN CONTENT LAYOUT */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h2.section-title {
            color: var(--secondary-color);
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--header-bg);
            padding-bottom: 5px;
            display: inline-block;
        }
        
        h2.section-title:hover { cursor: pointer; color: var(--primary-color); }

        /* CAROUSEL / BOOK SLIDERS */
        .carousel-wrapper {
            position: relative;
            margin-bottom: 50px;
        }

        .carousel {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 20px;
            scroll-behavior: smooth;
        }
        
        .carousel::-webkit-scrollbar { height: 8px; }
        .carousel::-webkit-scrollbar-thumb { background: var(--primary-color); border-radius: 4px; }
        .carousel::-webkit-scrollbar-track { background: var(--header-bg); border-radius: 4px; }

        .book-card {
            min-width: 150px;
            width: 150px;
            display: flex;
            flex-direction: column;
        }

        .book-cover {
            width: 100%;
            height: 220px;
            background-color: #ddd;
            object-fit: cover;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            border: 1px solid #e0dccc;
        }

        /* ACTION BUTTONS */
        .action-btn {
            width: 100%;
            padding: 8px 0;
            text-align: center;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            font-weight: bold;
            transition: opacity 0.2s;
        }
        
        .action-btn:hover { opacity: 0.85; }

        .btn-secondary { background: var(--secondary-color); color: white; border: none; }
        .btn-primary { background: var(--primary-color); color: white; border: none; }
        .btn-outline { background: white; color: var(--secondary-color); border: 2px solid var(--secondary-color); padding: 6px 0; }

        .nav-arrow {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--secondary-color);
            font-size: 20px;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-prev { left: -20px; pointer-events: none; opacity: 0.5; } 
        .nav-next { right: -20px; pointer-events: none; opacity: 0.5; }

        /* SEARCH MODAL */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(138, 118, 80, 0.4);
            backdrop-filter: blur(3px);
        }

        .modal-bg-close {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            cursor: default;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border: 2px solid var(--header-bg);
            width: 80%;
            max-width: 900px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1001;
        }

        .close-btn {
            color: var(--secondary-color);
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover { color: #000; }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .results-grid::-webkit-scrollbar { width: 8px; }
        .results-grid::-webkit-scrollbar-thumb { background: var(--header-bg); border-radius: 4px; }

        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: var(--secondary-color);
            font-size: 18px;
        }

        @media (max-width: 768px) {
            header { flex-direction: column; gap: 10px; padding: 10px; }
            .search-bar { width: 100%; margin: 10px 0; }
            .nav-links { display: none; } 
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <i class="fa-solid fa-book-open" style="margin-right:5px; color: var(--secondary-color);"></i>
            LIBRARY
        </div>
        
        <nav class="nav-links">
            <a href="#">My Books <i class="fa-solid fa-caret-down"></i></a>
        </nav>

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

    <main class="container">
        
        <?php if (empty($groupedBooks)): ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--secondary-color);">
                <i class="fa-solid fa-book-journal-whills" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <h2>Our shelves are currently empty!</h2>
                <p style="margin-top: 10px; color: #666;">Check back later or add some books from the admin dashboard.</p>
            </div>
            <?php else: ?>
                <?php foreach ($groupedBooks as $categoryName => $books): ?>
                    <h2 class="section-title"><?php echo htmlspecialchars($categoryName); ?></h2>
                    <div class="carousel-wrapper">
                        <div class="nav-arrow nav-prev"><i class="fa-solid fa-chevron-left"></i></div>
                        <div class="nav-arrow nav-next"><i class="fa-solid fa-chevron-right"></i></div>
                        
                        <div class="carousel">
                            <?php foreach ($books as $book): ?>
                            <div class="book-card">
                                <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                                
                                <button class="action-btn <?php echo htmlspecialchars($book['btn_class']); ?>">
                                    <?php echo htmlspecialchars($book['status']); ?>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

    </main>

    <?php if ($showModal): ?>
    <div id="searchModal" class="modal" style="display: block;">
        <a href="?" class="modal-bg-close"></a>
        
        <div class="modal-content">
            <a href="?" class="close-btn">&times;</a>
            
            <h2>Search Results for: <span style="color: var(--secondary-color);">"<?php echo htmlspecialchars($searchQuery); ?>"</span></h2>
            
            <div class="results-grid">
                <?php if (count($searchResults) > 0): ?>
                    <?php foreach ($searchResults as $book): ?>
                        <div class="book-card">
                            <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                            <p style="font-size: 13px; margin: 5px 0; font-weight: bold;"><?php echo htmlspecialchars($book['title']); ?></p>
                            <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Cat: <?php echo htmlspecialchars($book['category']); ?></p>
                            <button class="action-btn <?php echo htmlspecialchars($book['btn_class']); ?>"><?php echo htmlspecialchars($book['status']); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">No books found matching your criteria.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>