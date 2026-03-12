-- create tabels
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    discription VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    pdf_path VARCHAR(255) NULL,
    isUrl BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS issued_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    issue_date DATE NOT NULL,
    return_date DATE NULL,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

CREATE TABLE IF NOT EXISTS fines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    issued_book_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (issued_book_id) REFERENCES issued_books(id) ON DELETE CASCADE
);


-- all students password is 123456
INSERT INTO students (name,email,password) VALUES
('Rahul Sharma','rahul@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Amit Das','amit@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Priya Verma','priya@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Sneha Roy','sneha@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Arjun Singh','arjun@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');


-- add books
INSERT INTO books (name,author,category,discription,image_path,pdf_path,isUrl) VALUES

-- COMPUTER SCIENCE
('Clean Code','Robert C. Martin','Computer Science','Guide to writing clean code','https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg',NULL,TRUE),
('The Pragmatic Programmer','Andrew Hunt','Computer Science','Programming best practices','https://covers.openlibrary.org/b/isbn/9780201616224-L.jpg',NULL,TRUE),
('Introduction to Algorithms','Thomas H. Cormen','Computer Science','Algorithms reference','https://covers.openlibrary.org/b/isbn/9780262033848-L.jpg',NULL,TRUE),
('Design Patterns','Erich Gamma','Computer Science','Reusable software patterns','https://covers.openlibrary.org/b/isbn/9780201633610-L.jpg',NULL,TRUE),
('Code Complete','Steve McConnell','Computer Science','Software construction guide','https://covers.openlibrary.org/b/isbn/9780735619678-L.jpg',NULL,TRUE),
('Refactoring','Martin Fowler','Computer Science','Improving code structure','https://covers.openlibrary.org/b/isbn/9780201485677-L.jpg',NULL,TRUE),
('Structure and Interpretation of Computer Programs','Harold Abelson','Computer Science','Computer science classic','https://covers.openlibrary.org/b/isbn/9780262510875-L.jpg',NULL,TRUE),
('You Don’t Know JS','Kyle Simpson','Computer Science','Deep JavaScript guide','https://covers.openlibrary.org/b/isbn/9781491904244-L.jpg',NULL,TRUE),

-- DATA SCIENCE
('Hands-On Machine Learning','Aurelien Geron','Data Science','Machine learning with Python','https://covers.openlibrary.org/b/isbn/9781492032649-L.jpg',NULL,TRUE),
('Python for Data Analysis','Wes McKinney','Data Science','Data analysis techniques','https://covers.openlibrary.org/b/isbn/9781491957660-L.jpg',NULL,TRUE),
('Deep Learning','Ian Goodfellow','Data Science','Deep learning fundamentals','https://covers.openlibrary.org/b/isbn/9780262035613-L.jpg',NULL,TRUE),
('Pattern Recognition and Machine Learning','Christopher Bishop','Data Science','ML mathematical concepts','https://covers.openlibrary.org/b/isbn/9780387310732-L.jpg',NULL,TRUE),
('Data Science from Scratch','Joel Grus','Data Science','Building data science tools','https://covers.openlibrary.org/b/isbn/9781492041139-L.jpg',NULL,TRUE),
('Machine Learning Yearning','Andrew Ng','Data Science','ML project strategy','https://covers.openlibrary.org/b/isbn/9780999579509-L.jpg',NULL,TRUE),
('The Hundred-Page Machine Learning Book','Andriy Burkov','Data Science','Short ML guide','https://covers.openlibrary.org/b/isbn/9781999579500-L.jpg',NULL,TRUE),
('Practical Statistics for Data Scientists','Peter Bruce','Data Science','Statistics for ML','https://covers.openlibrary.org/b/isbn/9781491952962-L.jpg',NULL,TRUE),

-- SCIENCE
('A Brief History of Time','Stephen Hawking','Science','Cosmology explained','https://covers.openlibrary.org/b/isbn/9780553380163-L.jpg',NULL,TRUE),
('Cosmos','Carl Sagan','Science','Universe exploration','https://covers.openlibrary.org/b/isbn/9780345539434-L.jpg',NULL,TRUE),
('Astrophysics for People in a Hurry','Neil deGrasse Tyson','Science','Quick astrophysics guide','https://covers.openlibrary.org/b/isbn/9780393609394-L.jpg',NULL,TRUE),
('The Elegant Universe','Brian Greene','Science','String theory explained','https://covers.openlibrary.org/b/isbn/9780393338102-L.jpg',NULL,TRUE),
('The Selfish Gene','Richard Dawkins','Science','Evolution theory','https://covers.openlibrary.org/b/isbn/9780192860927-L.jpg',NULL,TRUE),
('The Gene','Siddhartha Mukherjee','Science','Genetics history','https://covers.openlibrary.org/b/isbn/9781476733524-L.jpg',NULL,TRUE),
('The Origin of Species','Charles Darwin','Science','Evolution classic','https://covers.openlibrary.org/b/isbn/9781509827695-L.jpg',NULL,TRUE),
('Surely You’re Joking Mr Feynman','Richard Feynman','Science','Physics stories','https://covers.openlibrary.org/b/isbn/9780393355628-L.jpg',NULL,TRUE),

-- BUSINESS
('Rich Dad Poor Dad','Robert Kiyosaki','Business','Personal finance lessons','https://covers.openlibrary.org/b/isbn/9781612680194-L.jpg',NULL,TRUE),
('The Lean Startup','Eric Ries','Business','Startup methodology','https://covers.openlibrary.org/b/isbn/9780307887894-L.jpg',NULL,TRUE),
('Zero to One','Peter Thiel','Business','Startup innovation','https://covers.openlibrary.org/b/isbn/9780804139298-L.jpg',NULL,TRUE),
('Atomic Habits','James Clear','Business','Habit improvement','https://covers.openlibrary.org/b/isbn/9780735211292-L.jpg',NULL,TRUE),
('Thinking Fast and Slow','Daniel Kahneman','Business','Psychology of decision making','https://covers.openlibrary.org/b/isbn/9780374533557-L.jpg',NULL,TRUE),
('Good to Great','Jim Collins','Business','Business research study','https://covers.openlibrary.org/b/isbn/9780066620992-L.jpg',NULL,TRUE),
('The Intelligent Investor','Benjamin Graham','Business','Stock investing','https://covers.openlibrary.org/b/isbn/9780060555665-L.jpg',NULL,TRUE),
('The 7 Habits of Highly Effective People','Stephen Covey','Business','Self improvement','https://covers.openlibrary.org/b/isbn/9781982137274-L.jpg',NULL,TRUE),

-- ENTREPRENEURSHIP
('Start With Why','Simon Sinek','Entrepreneurship','Leadership inspiration','https://covers.openlibrary.org/b/isbn/9781591846444-L.jpg',NULL,TRUE),
('Rework','Jason Fried','Entrepreneurship','Modern business ideas','https://covers.openlibrary.org/b/isbn/9780307463746-L.jpg',NULL,TRUE),
('Crushing It','Gary Vaynerchuk','Entrepreneurship','Personal brand success','https://covers.openlibrary.org/b/isbn/9780062674678-L.jpg',NULL,TRUE),
('The $100 Startup','Chris Guillebeau','Entrepreneurship','Start small businesses','https://covers.openlibrary.org/b/isbn/9780307951526-L.jpg',NULL,TRUE),
('Hooked','Nir Eyal','Entrepreneurship','Product psychology','https://covers.openlibrary.org/b/isbn/9781591847786-L.jpg',NULL,TRUE),
('Measure What Matters','John Doerr','Entrepreneurship','OKR system','https://covers.openlibrary.org/b/isbn/9780525536222-L.jpg',NULL,TRUE),
('The Hard Thing About Hard Things','Ben Horowitz','Entrepreneurship','Startup leadership','https://covers.openlibrary.org/b/isbn/9780062273208-L.jpg',NULL,TRUE),
('Built to Last','Jim Collins','Entrepreneurship','Visionary companies','https://covers.openlibrary.org/b/isbn/9780060516406-L.jpg',NULL,TRUE);


-- add data in issued_books
INSERT INTO issued_books (book_id,student_id,issue_date,return_date) VALUES
(1,1,'2026-02-01','2026-02-08'),
(2,1,'2026-02-02','2026-02-09'),
(3,2,'2026-02-03','2026-02-10'),
(4,3,'2026-02-04','2026-02-11'),
(5,4,'2026-02-05','2026-02-12'),
(6,5,'2026-02-06','2026-02-13'),
(7,2,'2026-02-07','2026-02-14'),
(8,3,'2026-02-08','2026-02-15'),
(9,4,'2026-02-09','2026-02-16'),
(10,5,'2026-02-10','2026-02-17'),
(11,1,'2026-02-11','2026-02-18'),
(12,2,'2026-02-12','2026-02-19'),
(13,3,'2026-02-13','2026-02-20'),
(14,4,'2026-02-14','2026-02-21'),
(15,5,'2026-02-15','2026-02-22');

-- add data in fines
INSERT INTO fines (issued_book_id,amount) VALUES
(1,50),
(2,40),
(3,30),
(4,20),
(5,10),
(6,60),
(7,70),
(8,80);