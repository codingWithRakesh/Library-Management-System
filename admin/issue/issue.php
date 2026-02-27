<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Library — Books List</title>
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

        /* page container */
        /* .page{
      max-width:1200px;
      margin:20px auto;
      padding:0 20px 60px;
    } */

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
                    <img src="../../assets/images/OOP.jpeg" alt="Logo">
                </div>
                <h1 class="brand-name">Admin Dashboard</h1>
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

            <form class="search-form" method="get" action="">
                <input class="searchInput" name="q" type="text" placeholder="Search books..." />
                <button class="primary-btn" type="submit">Submit</button>
            </form>
        </div>

        <!-- TABLE -->
        <div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th class="table-head">Serial no.</th>
        <th class="table-head">User name</th>
        <th class="table-head">Book ID</th>
        <th class="table-head">Book Name</th>
        <th class="table-head">Issue Date</th>
        <th class="table-head">Last Date</th>
        <th class="table-head">Fine</th>
        <th class="table-head">Delete</th>
      </tr>
    </thead>

    <tbody>
      <tr><td class="table-body">1</td><td class="table-body">Amit Sharma</td><td class="table-body">B001</td><td class="table-body">Introduction to Computer Science</td><td class="table-body">01-08-2025</td><td class="table-body">15-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">2</td><td class="table-body">Riya Sen</td><td class="table-body">B002</td><td class="table-body">Advanced Mathematics</td><td class="table-body">02-08-2025</td><td class="table-body">16-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">3</td><td class="table-body">Rahul Verma</td><td class="table-body">B003</td><td class="table-body">Discrete Mathematics</td><td class="table-body">03-08-2025</td><td class="table-body">17-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">4</td><td class="table-body">Neha Gupta</td><td class="table-body">B004</td><td class="table-body">Data Structures & Algorithms</td><td class="table-body">04-08-2025</td><td class="table-body">18-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">5</td><td class="table-body">Arjun Malhotra</td><td class="table-body">B005</td><td class="table-body">Operating Systems</td><td class="table-body">05-08-2025</td><td class="table-body">19-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">6</td><td class="table-body">Priya Das</td><td class="table-body">B006</td><td class="table-body">Database System Concepts</td><td class="table-body">06-08-2025</td><td class="table-body">20-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">7</td><td class="table-body">Sourav Banerjee</td><td class="table-body">B007</td><td class="table-body">Computer Networks</td><td class="table-body">07-08-2025</td><td class="table-body">21-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">8</td><td class="table-body">Kunal Mehta</td><td class="table-body">B008</td><td class="table-body">Software Engineering</td><td class="table-body">08-08-2025</td><td class="table-body">22-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">9</td><td class="table-body">Ananya Roy</td><td class="table-body">B009</td><td class="table-body">Artificial Intelligence</td><td class="table-body">09-08-2025</td><td class="table-body">23-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">10</td><td class="table-body">Vikram Singh</td><td class="table-body">B010</td><td class="table-body">Machine Learning</td><td class="table-body">10-08-2025</td><td class="table-body">24-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">11</td><td class="table-body">Pooja Nair</td><td class="table-body">B011</td><td class="table-body">Compiler Design</td><td class="table-body">11-08-2025</td><td class="table-body">25-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">12</td><td class="table-body">Rohit Chatterjee</td><td class="table-body">B012</td><td class="table-body">Linear Algebra</td><td class="table-body">12-08-2025</td><td class="table-body">26-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">13</td><td class="table-body">Sneha Kapoor</td><td class="table-body">B013</td><td class="table-body">Probability & Statistics</td><td class="table-body">13-08-2025</td><td class="table-body">27-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">14</td><td class="table-body">Abhishek Mishra</td><td class="table-body">B014</td><td class="table-body">Cryptography</td><td class="table-body">14-08-2025</td><td class="table-body">28-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">15</td><td class="table-body">Ishita Mukherjee</td><td class="table-body">B015</td><td class="table-body">Human Computer Interaction</td><td class="table-body">15-08-2025</td><td class="table-body">29-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">16</td><td class="table-body">Manish Yadav</td><td class="table-body">B016</td><td class="table-body">Numerical Methods</td><td class="table-body">16-08-2025</td><td class="table-body">30-08-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">17</td><td class="table-body">Tanvi Joshi</td><td class="table-body">B017</td><td class="table-body">Information Retrieval</td><td class="table-body">17-08-2025</td><td class="table-body">31-08-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">18</td><td class="table-body">Aditya Kulkarni</td><td class="table-body">B018</td><td class="table-body">Digital Logic</td><td class="table-body">18-08-2025</td><td class="table-body">01-09-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">19</td><td class="table-body">Nandini Ghosh</td><td class="table-body">B019</td><td class="table-body">Pattern Recognition</td><td class="table-body">19-08-2025</td><td class="table-body">02-09-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">20</td><td class="table-body">Siddharth Jain</td><td class="table-body">B020</td><td class="table-body">Theory of Computation</td><td class="table-body">20-08-2025</td><td class="table-body">03-09-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">21</td><td class="table-body">Kritika Bansal</td><td class="table-body">B021</td><td class="table-body">Data Mining</td><td class="table-body">21-08-2025</td><td class="table-body">04-09-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">22</td><td class="table-body">Rakesh Patel</td><td class="table-body">B022</td><td class="table-body">Embedded Systems</td><td class="table-body">22-08-2025</td><td class="table-body">05-09-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">23</td><td class="table-body">Mehul Shah</td><td class="table-body">B023</td><td class="table-body">Computer Graphics</td><td class="table-body">23-08-2025</td><td class="table-body">06-09-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">24</td><td class="table-body">Shreya Iyer</td><td class="table-body">B024</td><td class="table-body">Big Data Analytics</td><td class="table-body">24-08-2025</td><td class="table-body">07-09-2025</td><td class="table-body">Applicable</td><td class="table-body"><button>Delete</button></td></tr>

      <tr><td class="table-body">25</td><td class="table-body">Devraj Sinha</td><td class="table-body">B025</td><td class="table-body">Introduction to Algorithms</td><td class="table-body">25-08-2025</td><td class="table-body">08-09-2025</td><td class="table-body">Not Applicable</td><td class="table-body"><button>Delete</button></td></tr>
    </tbody>
  </table>
</div>

    </div><!-- .page -->
</body>

</html>