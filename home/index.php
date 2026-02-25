<?php
// COMPLETE DATA SIMULATION - 10 Categories, 10 Books Each (100 Total)
$allBooks = [
    // --- FANTASY ---
    ["title" => "A Court of Mist and Fury", "author" => "Sarah J. Maas", "category" => "Fantasy", "image" => "https://covers.openlibrary.org/b/id/8259449-L.jpg", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "A Game of Thrones", "author" => "George R. R. Martin", "category" => "Fantasy", "image" => "https://covers.openlibrary.org/b/id/8106203-L.jpg", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Hobbit", "author" => "J.R.R. Tolkien", "category" => "Fantasy", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=The+Hobbit", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "The Name of the Wind", "author" => "Patrick Rothfuss", "category" => "Fantasy", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Name+of+Wind", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Mistborn", "author" => "Brandon Sanderson", "category" => "Fantasy", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Mistborn", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Harry Potter", "author" => "J.K. Rowling", "category" => "Fantasy", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Harry+Potter", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "The Way of Kings", "author" => "Brandon Sanderson", "category" => "Fantasy", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Way+of+Kings", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Witcher", "author" => "Andrzej Sapkowski", "category" => "Fantasy", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=The+Witcher", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "American Gods", "author" => "Neil Gaiman", "category" => "Fantasy", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=American+Gods", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Eragon", "author" => "Christopher Paolini", "category" => "Fantasy", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Eragon", "status" => "Borrow", "btn_class" => "btn-primary"],

    // --- MYSTERY ---
    ["title" => "The Adventures of Sherlock Holmes", "author" => "A. Conan Doyle", "category" => "Mystery", "image" => "https://covers.openlibrary.org/b/id/12646467-L.jpg", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "And Then There Were None", "author" => "Agatha Christie", "category" => "Mystery", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=And+Then...", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Gone Girl", "author" => "Gillian Flynn", "category" => "Mystery", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Gone+Girl", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "The Girl with the Dragon Tattoo", "author" => "Stieg Larsson", "category" => "Mystery", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Dragon+Tattoo", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Big Little Lies", "author" => "Liane Moriarty", "category" => "Mystery", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Big+Little+Lies", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "The Da Vinci Code", "author" => "Dan Brown", "category" => "Mystery", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Da+Vinci+Code", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "The Silent Patient", "author" => "Alex Michaelides", "category" => "Mystery", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Silent+Patient", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Rebecca", "author" => "Daphne du Maurier", "category" => "Mystery", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Rebecca", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "In the Woods", "author" => "Tana French", "category" => "Mystery", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=In+the+Woods", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "The Girl on the Train", "author" => "Paula Hawkins", "category" => "Mystery", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Girl+on+Train", "status" => "Borrow", "btn_class" => "btn-primary"],

    // --- CLASSIC ---
    ["title" => "Love Letters", "author" => "Unknown", "category" => "Classic", "image" => "https://covers.openlibrary.org/b/id/6979201-L.jpg", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Pride and Prejudice", "author" => "Jane Austen", "category" => "Classic", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Pride+%26+Prejudice", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "1984", "author" => "George Orwell", "category" => "Classic", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=1984", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "To Kill a Mockingbird", "author" => "Harper Lee", "category" => "Classic", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Mockingbird", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Moby Dick", "author" => "Herman Melville", "category" => "Classic", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Moby+Dick", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Great Expectations", "author" => "Charles Dickens", "category" => "Classic", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Expectations", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Jane Eyre", "author" => "Charlotte Brontë", "category" => "Classic", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Jane+Eyre", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Wuthering Heights", "author" => "Emily Brontë", "category" => "Classic", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Wuthering", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "The Great Gatsby", "author" => "F. Scott Fitzgerald", "category" => "Classic", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Gatsby", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Frankenstein", "author" => "Mary Shelley", "category" => "Classic", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Frankenstein", "status" => "Preview", "btn_class" => "btn-outline"],

    // --- SCIENCE ---
    ["title" => "Cosmos", "author" => "Carl Sagan", "category" => "Science", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Cosmos", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "A Brief History of Time", "author" => "Stephen Hawking", "category" => "Science", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=History+of+Time", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "The Selfish Gene", "author" => "Richard Dawkins", "category" => "Science", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Selfish+Gene", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Sapiens", "author" => "Yuval Noah Harari", "category" => "Science", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Sapiens", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Astrophysics for People in a Hurry", "author" => "Neil deGrasse Tyson", "category" => "Science", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Astrophysics", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "The Immortal Life of Henrietta Lacks", "author" => "Rebecca Skloot", "category" => "Science", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Henrietta+Lacks", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Silent Spring", "author" => "Rachel Carson", "category" => "Science", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Silent+Spring", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Origin of Species", "author" => "Charles Darwin", "category" => "Science", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Origin+Species", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Thinking, Fast and Slow", "author" => "Daniel Kahneman", "category" => "Science", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Fast+%26+Slow", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Gene", "author" => "Siddhartha Mukherjee", "category" => "Science", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=The+Gene", "status" => "Preview", "btn_class" => "btn-outline"],

    // --- COMPUTER SCIENCE ---
    ["title" => "Introduction to Algorithms", "author" => "Thomas H. Cormen", "category" => "Computer Science", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Algorithms", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Clean Code", "author" => "Robert C. Martin", "category" => "Computer Science", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Clean+Code", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Design Patterns", "author" => "Erich Gamma", "category" => "Computer Science", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Design+Patterns", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Code Complete", "author" => "Steve McConnell", "category" => "Computer Science", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Code+Complete", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Pragmatic Programmer", "author" => "Andrew Hunt", "category" => "Computer Science", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Pragmatic", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Structure & Interpretation", "author" => "Harold Abelson", "category" => "Computer Science", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=SICP", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Compilers", "author" => "Alfred V. Aho", "category" => "Computer Science", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Compilers", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Theory of Computation", "author" => "Michael Sipser", "category" => "Computer Science", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Computation", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Code", "author" => "Charles Petzold", "category" => "Computer Science", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Code", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Artificial Intelligence", "author" => "Stuart Russell", "category" => "Computer Science", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=AI", "status" => "Preview", "btn_class" => "btn-outline"],

    // --- CODING LANGUAGE ---
    ["title" => "The C Programming Language", "author" => "Brian W. Kernighan", "category" => "Coding Language", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=C+Language", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Eloquent JavaScript", "author" => "Marijn Haverbeke", "category" => "Coding Language", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=JavaScript", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Effective Java", "author" => "Joshua Bloch", "category" => "Coding Language", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Java", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Fluent Python", "author" => "Luciano Ramalho", "category" => "Coding Language", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Python", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Learning PHP, MySQL & JS", "author" => "Robin Nixon", "category" => "Coding Language", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=PHP+MySQL", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Head First Java", "author" => "Kathy Sierra", "category" => "Coding Language", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Head+First", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "You Don't Know JS", "author" => "Kyle Simpson", "category" => "Coding Language", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=YDK+JS", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "C++ Primer", "author" => "Stanley B. Lippman", "category" => "Coding Language", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=C%2B%2B+Primer", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Learning Perl", "author" => "Randal L. Schwartz", "category" => "Coding Language", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Perl", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Programming Rust", "author" => "Jim Blandy", "category" => "Coding Language", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Rust", "status" => "Preview", "btn_class" => "btn-outline"],

    // --- TRUE CRIME ---
    ["title" => "In Cold Blood", "author" => "Truman Capote", "category" => "True Crime", "image" => "https://covers.openlibrary.org/b/id/8231731-L.jpg", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "I'll Be Gone in the Dark", "author" => "Michelle McNamara", "category" => "True Crime", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Gone+in+Dark", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Stranger Beside Me", "author" => "Ann Rule", "category" => "True Crime", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Stranger+Beside", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Helter Skelter", "author" => "Vincent Bugliosi", "category" => "True Crime", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Helter+Skelter", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Killers of the Flower Moon", "author" => "David Grann", "category" => "True Crime", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Flower+Moon", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Devil in the White City", "author" => "Erik Larson", "category" => "True Crime", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=White+City", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Zodiac", "author" => "Robert Graysmith", "category" => "True Crime", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Zodiac", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Mindhunter", "author" => "John E. Douglas", "category" => "True Crime", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Mindhunter", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Columbine", "author" => "Dave Cullen", "category" => "True Crime", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Columbine", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Catch and Kill", "author" => "Ronan Farrow", "category" => "True Crime", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Catch+%26+Kill", "status" => "Preview", "btn_class" => "btn-outline"],

    // --- YOUNG ADULT ---
    ["title" => "Speak", "author" => "Laurie Halse Anderson", "category" => "Young Adult", "image" => "https://covers.openlibrary.org/b/id/6429994-L.jpg", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Looking for Alaska", "author" => "John Green", "category" => "Young Adult", "image" => "https://covers.openlibrary.org/b/id/8282303-L.jpg", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Hunger Games", "author" => "Suzanne Collins", "category" => "Young Adult", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Hunger+Games", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Divergent", "author" => "Veronica Roth", "category" => "Young Adult", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Divergent", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "The Fault in Our Stars", "author" => "John Green", "category" => "Young Adult", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Fault+in+Stars", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Maze Runner", "author" => "James Dashner", "category" => "Young Adult", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Maze+Runner", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Twilight", "author" => "Stephenie Meyer", "category" => "Young Adult", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Twilight", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "The Book Thief", "author" => "Markus Zusak", "category" => "Young Adult", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Book+Thief", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Eleanor & Park", "author" => "Rainbow Rowell", "category" => "Young Adult", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Eleanor+Park", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Perks of Being a Wallflower", "author" => "Stephen Chbosky", "category" => "Young Adult", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Wallflower", "status" => "Preview", "btn_class" => "btn-outline"],

    // --- SCI-FI ---
    ["title" => "Dune", "author" => "Frank Herbert", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Dune", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Foundation", "author" => "Isaac Asimov", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Foundation", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Neuromancer", "author" => "William Gibson", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Neuromancer", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "The Martian", "author" => "Andy Weir", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=The+Martian", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Snow Crash", "author" => "Neal Stephenson", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Snow+Crash", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Hitchhiker's Guide", "author" => "Douglas Adams", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Hitchhiker", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Ender's Game", "author" => "Orson Scott Card", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Ender's+Game", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Brave New World", "author" => "Aldous Huxley", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Brave+New", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Fahrenheit 451", "author" => "Ray Bradbury", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Fahrenheit", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Left Hand of Darkness", "author" => "Ursula K. Le Guin", "category" => "Sci-Fi", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Left+Hand", "status" => "Preview", "btn_class" => "btn-outline"],

    // --- BIOGRAPHY ---
    ["title" => "Steve Jobs", "author" => "Walter Isaacson", "category" => "Biography", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Steve+Jobs", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Becoming", "author" => "Michelle Obama", "category" => "Biography", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Becoming", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Diary of a Young Girl", "author" => "Anne Frank", "category" => "Biography", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Anne+Frank", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Einstein", "author" => "Walter Isaacson", "category" => "Biography", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Einstein", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Long Walk to Freedom", "author" => "Nelson Mandela", "category" => "Biography", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Mandela", "status" => "Preview", "btn_class" => "btn-outline"],
    ["title" => "Autobiography of Malcolm X", "author" => "Malcolm X", "category" => "Biography", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Malcolm+X", "status" => "Read", "btn_class" => "btn-primary"],
    ["title" => "Alexander Hamilton", "author" => "Ron Chernow", "category" => "Biography", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Hamilton", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "Churchill", "author" => "Andrew Roberts", "category" => "Biography", "image" => "https://placehold.co/150x220/8e977d/ffffff?text=Churchill", "status" => "Checked Out", "btn_class" => "btn-secondary"],
    ["title" => "Shoe Dog", "author" => "Phil Knight", "category" => "Biography", "image" => "https://placehold.co/150x220/8a7650/ffffff?text=Shoe+Dog", "status" => "Borrow", "btn_class" => "btn-primary"],
    ["title" => "The Wright Brothers", "author" => "David McCullough", "category" => "Biography", "image" => "https://placehold.co/150x220/dbcea5/8a7650?text=Wright+Bros", "status" => "Preview", "btn_class" => "btn-outline"]
];

// Dynamically group books by category so we can loop through them in the HTML
$groupedBooks = [];
foreach ($allBooks as $book) {
    $groupedBooks[$book['category']][] = $book;
}

// --- PURE PHP SEARCH LOGIC ---
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