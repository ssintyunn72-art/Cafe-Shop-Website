<?php
include "../session.php";
include "../connection.php";

/* PRODUCTS COUNT */
$productCount = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) as total FROM products")
)['total'];

/* MEMBERS COUNT */
$memberCount = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) as total FROM members")
)['total'];

/* ORDERS COUNT */
$orderCount = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) as total FROM orders")
)['total'];

/* STAFF COUNT (if you have users table) */
$staffCount = mysqli_fetch_assoc(
    mysqli_query($connection, "SELECT COUNT(*) as total FROM users WHERE roleid != 3")
)['total'];

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$staffName = htmlspecialchars($_SESSION['name'] ?? 'Staff', ENT_QUOTES, 'UTF-8');
$today = date("l, j F Y");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Cafe Shop</title>

    <script>
        (function(){
            var saved = localStorage.getItem('cafe-theme');
            var theme = saved ? saved : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if(theme === 'dark'){
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts: Fraunces (display) + IBM Plex Mono (data/labels) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root{
            --espresso-950:#150e0a;
            --espresso-900:#1e130d;
            --espresso-800:#2c1c15;
            --cream-bg:#f7f3ec;
            --cream:#f2e9da;
            --cream-dim:#a89a86;
            --ink:#2c1c15;
            --ink-dim:#7a6a5c;
            --amber:#d69a3d;
            --amber-bright:#eab868;
            --rust:#9c4a34;
            --sage:#6f8a5c;
            --line:#e7ded1;
            --surface:#ffffff;
            --shadow-soft:0 12px 24px -14px rgba(44,28,21,.25);
            --sidebar-w:250px;
        }

        html[data-theme="dark"]{
            --cream-bg:#150e0a;
            --ink:#f2e9da;
            --ink-dim:#a89a86;
            --line:rgba(242,233,218,.12);
            --surface:#211510;
            --shadow-soft:0 12px 24px -14px rgba(0,0,0,.6);
        }

        *{ -webkit-font-smoothing: antialiased; }

        html{ transition:background .2s ease; }

        body{
            background:var(--cream-bg);
            font-family:'IBM Plex Mono', monospace;
            color:var(--ink);
            transition:background .2s ease, color .2s ease;
        }

        h1,h2,h3,h4,.font-display{
            font-family:'Fraunces', serif;
        }

        /* ===== Sidebar ===== */
        .sidebar{
            position:fixed;
            top:0;
            left:0;
            width:var(--sidebar-w);
            height:100vh;
            background:var(--espresso-950);
            color:var(--cream);
            display:flex;
            flex-direction:column;
            z-index:1040;
            transition:transform .25s ease;
        }

        .sidebar-brand{
    display:flex;
    align-items:center;
    gap:10px;
    padding:24px 22px;
    border-bottom:1px solid rgba(242,233,218,.1);
    transition:padding .2s ease;
}
        .sidebar-brand .mark{ font-size:1.4rem; }
        .sidebar-brand span{
            font-family:'Fraunces', serif;
            font-weight:600;
            font-size:1.65rem;
            letter-spacing:.01em;
        }

        .sidebar-nav{
            flex:1;
            overflow-y:auto;
            padding:14px 0;
            scrollbar-width:none;      
            -ms-overflow-style:none;   
        }
        .sidebar-nav::-webkit-scrollbar{
            display:none;              
        }

        .sidebar-section{
            font-size:.62rem;
            letter-spacing:.16em;
            text-transform:uppercase;
            color:var(--cream-dim);
            padding:14px 22px 8px;
        }

        .sidebar-nav a{
            display:flex;
            align-items:center;
            gap:12px;
            color:var(--cream-dim);
            text-decoration:none;
            padding:11px 22px;
            font-size:.82rem;
            letter-spacing:.02em;
            border-left:3px solid transparent;
            transition:background .15s ease, color .15s ease;
        }

        .sidebar-nav a i{ font-size:1rem; width:18px; text-align:center; }

        .sidebar-nav a:hover{
            background:rgba(242,233,218,.05);
            color:var(--cream);
        }

        .sidebar-nav a.active{
            background:rgba(214,154,61,.12);
            color:var(--amber-bright);
            border-left-color:var(--amber);
        }

        .sidebar-foot{
            padding:14px 8px 18px;
            border-top:1px solid rgba(242,233,218,.1);
        }
        .sidebar-foot a{
            display:flex;
            align-items:center;
            gap:12px;
            color:var(--cream-dim);
            text-decoration:none;
            padding:11px 14px;
            font-size:.82rem;
            border-radius:8px;
        }
        .sidebar-foot a:hover{
            background:rgba(156,74,52,.18);
            color:#e88a6f;
        }

        /* .sidebar-toggle{
            display:none;
            position:fixed;
            top:16px;
            left:16px;
            z-index:1050;
            background:var(--espresso-950);
            color:var(--cream);
            border:none;
            border-radius:8px;
            width:40px;
            height:40px;
        } */
            .sidebar-toggle{
   display:none;
    position:fixed;
    top:16px;
    left:16px;
    z-index:1060;
    background:var(--espresso-950);
    color:var(--cream);
    border:none;
    border-radius:8px;
    width:40px;
    height:40px;
    align-items:center;
    justify-content:center;
}
.sidebar-toggle i{
    color:var(--cream);
    font-size:1.25rem;
    line-height:1;
    pointer-events:none;
}

        .sidebar-backdrop{
            display:none;
            position:fixed;
            inset:0;
            background:rgba(21,14,10,.5);
            z-index:1030;
        }

        /* ===== Content ===== */
        .content{
            margin-left:var(--sidebar-w);
            padding:28px 34px 60px;
        }

        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            flex-wrap:wrap;
            gap:16px;
            margin-bottom:28px;
        }

        .topbar .eyebrow{
            font-size:.68rem;
            letter-spacing:.16em;
            text-transform:uppercase;
            color:var(--amber);
            margin-bottom:4px;
        }

        .topbar h1{
            font-size:1.7rem;
            font-weight:600;
            margin:0;
            color:var(--ink);
        }

        .topbar .date{
            font-size:.75rem;
            color:var(--ink-dim);
            margin-top:4px;
        }

        .avatar-chip{
            display:flex;
            align-items:center;
            gap:10px;
            background:var(--surface);
            border:1px solid var(--line);
            border-radius:999px;
            padding:6px 16px 6px 6px;
            transition:background .2s ease, border-color .2s ease;
        }
        .avatar-chip .avatar{
            width:34px; height:34px;
            border-radius:50%;
            background:var(--amber);
            color:var(--espresso-950);
            display:flex; align-items:center; justify-content:center;
            font-weight:600;
            font-family:'Fraunces', serif;
        }
        .avatar-chip .name{ font-size:.78rem; color:var(--ink); }
        .avatar-chip .role{ font-size:.64rem; color:var(--ink-dim); letter-spacing:.05em; }

        .topbar-right{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .theme-toggle{
            width:38px;
            height:38px;
            border-radius:50%;
            border:1px solid var(--line);
            background:var(--surface);
            color:var(--amber);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1rem;
            transition:background .2s ease, border-color .2s ease, transform .15s ease;
            flex:0 0 auto;
        }
        .theme-toggle:hover{
            border-color:var(--amber);
            transform:translateY(-1px);
        }
        

        /* ===== Stat cards ===== */
        .stat-card{
            background:var(--surface);
            border:1px solid var(--line);
            border-radius:14px;
            padding:20px 22px;
            height:100%;
            transition:transform .15s ease, box-shadow .15s ease, background .2s ease, border-color .2s ease;
        }
        .stat-card:hover{
            transform:translateY(-2px);
            box-shadow:var(--shadow-soft);
        }

        .stat-icon{
            width:42px; height:42px;
            border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            font-size:1.15rem;
            margin-bottom:14px;
        }
        .stat-icon.amber{ background:rgba(214,154,61,.14); color:var(--amber); }
        .stat-icon.rust{ background:rgba(156,74,52,.14); color:var(--rust); }
        .stat-icon.sage{ background:rgba(111,138,92,.14); color:var(--sage); }
        .stat-icon.ink{ background:rgba(44,28,21,.08); color:var(--ink); }

        .stat-label{
            font-size:.68rem;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--ink-dim);
            margin-bottom:6px;
        }

        .stat-value{
            font-family:'Fraunces', serif;
            font-weight:600;
            font-size:2rem;
            line-height:1;
            color:var(--ink);
        }

        .stat-trend{
            font-size:.68rem;
            color:var(--ink-dim);
            margin-top:8px;
        }

        /* ===== Panels ===== */
        .panel{
            background:var(--surface);
            border:1px solid var(--line);
            border-radius:14px;
            padding:22px 24px;
            height:100%;
            transition:background .2s ease, border-color .2s ease;
        }

        .panel-head{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:16px;
        }
        .panel-head h5{
            font-family:'Fraunces', serif;
            font-weight:600;
            font-size:1.05rem;
            margin:0;
        }
        .panel-head a{
            font-size:.7rem;
            letter-spacing:.06em;
            color:var(--amber);
            text-decoration:none;
        }
        .panel-head a:hover{ color:var(--rust); }

        .empty-state{
            text-align:center;
            padding:34px 10px;
            color:var(--ink-dim);
        }
        .empty-state i{
            font-size:1.7rem;
            color:var(--cream-dim);
            margin-bottom:10px;
            display:block;
        }
        .empty-state p{
            font-size:.78rem;
            margin:0;
        }

        .quick-list a{
            display:flex;
            align-items:center;
            gap:12px;
            padding:11px 0;
            border-bottom:1px dashed var(--line);
            color:var(--ink);
            text-decoration:none;
            font-size:.8rem;
        }
        .quick-list a:last-child{ border-bottom:none; }
        .quick-list a i{
            color:var(--amber);
            width:18px;
        }
        .quick-list a:hover{ color:var(--rust); }
        .quick-list a:hover i{ color:var(--rust); }

        html[data-theme="dark"] .stat-icon.amber{ background:rgba(214,154,61,.18); }
        html[data-theme="dark"] .stat-icon.rust{ background:rgba(156,74,52,.2); }
        html[data-theme="dark"] .stat-icon.sage{ background:rgba(111,138,92,.2); }
        html[data-theme="dark"] .stat-icon.ink{ background:rgba(242,233,218,.1); }

     @media (max-width: 991.98px){
    .sidebar{ transform:translateX(-100%); }
    .sidebar.show{ transform:translateX(0); }
    .content{ margin-left:0; padding:80px 18px 40px; }
    .sidebar-toggle{ display:flex; }          /* default visible on mobile */
    .sidebar-toggle.hide-toggle{ display:none !important; } /* hidden only while sidebar open */
    .sidebar-backdrop.show{ display:block; }
    body.sidebar-open{ overflow:hidden; }
}
    </style>
</head>

<body>

<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
    <i class="bi bi-list fs-4"></i>
</button>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <!-- <span class="mark">☕</span> -->
        <span>Brew Lounge</span>
    </div>
<!-- Side Bar  -->
    <nav class="sidebar-nav">
        <div class="sidebar-section">Overview</div>
        <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a>

        <div class="sidebar-section">Catalog</div>
        <a href="categories.php"><i class="bi bi-grid"></i> Categories</a>
        <!-- <a href="categories.php"><i class="bi bi-cup-hot"></i> Products</a> -->
        <a href="#"><i class="bi bi-megaphone"></i> Promotions</a>

        <div class="sidebar-section">People</div>
        <a href="member.php"><i class="bi bi-people"></i> Members</a>
        <a href="orders.php"><i class="bi bi-cart"></i> Orders</a>
        <a href="staff.php"><i class="bi bi-person-badge"></i> Staff</a>

        <div class="sidebar-section">Feedback</div>
        <a href="#"><i class="bi bi-star"></i> Feedback</a>
        <a href="#"><i class="bi bi-envelope"></i> Contact Messages</a>
    </nav>
<!-- Side Bar  -->

    <div class="sidebar-foot">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

</div>

<div class="content">

    <div class="topbar">
        <div>
            <!-- <div class="eyebrow">Back Office</div> -->
            <h1>Welcome, <?php echo $staffName; ?></h1>
            <div class="date"><?php echo $today; ?></div>
        </div>

        <div class="topbar-right">
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle day/night mode">
                <i class="bi bi-moon-stars" id="themeIcon"></i>
            </button>

            <div class="avatar-chip">
                <div class="avatar"><?php echo strtoupper(substr($staffName, 0, 1)); ?></div>
                <div>
                    <div class="name"><?php echo $staffName; ?></div>
                    <div class="role">Administrator</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="row g-3">

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon amber"><i class="bi bi-cup-hot"></i></div>
                <div class="stat-label">Products</div>
                <div class="stat-value"><?php echo $productCount; ?></div>
                <div class="stat-trend">No items listed yet</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon sage"><i class="bi bi-people"></i></div>
                <div class="stat-label">Members</div>
                <div class="stat-value"><?php echo $memberCount; ?></div>
                <div class="stat-trend">No members registered</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon rust"><i class="bi bi-cart"></i></div>
                <div class="stat-label">Orders</div>
                <div class="stat-value"><?php echo $orderCount; ?></div>
                <div class="stat-trend">No orders placed yet</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon ink"><i class="bi bi-person-badge"></i></div>
                <div class="stat-label">Staff</div>
                <div class="stat-value"><?php echo $staffCount; ?></div>
                <div class="stat-trend">No staff added yet</div>
            </div>
        </div>

    </div>

    <!-- Panels -->
    <div class="row g-3 mt-1">

        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-head">
                    <h5>Recent Orders</h5>
                    <a href="orders.php">View all <i class="bi bi-arrow-right"></i></a>
                </div>

                <?php
$sql = "SELECT * FROM orders ORDER BY orderid DESC LIMIT 5";
$result = mysqli_query($connection, $sql);
?>

<div class="quick-list">

<?php if (mysqli_num_rows($result) > 0) { ?>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>

        <a href="orders/receipt.php?orderid=<?php echo $row['orderid']; ?>">
            <i class="bi bi-receipt"></i>
            Order #<?php echo $row['orderid']; ?>
            - <?php echo $row['totalamount']; ?> MMK
        </a>

    <?php } ?>

<?php } else { ?>

    <div class="empty-state">
        <i class="bi bi-receipt"></i>
        <p>No orders yet. New orders will show up here.</p>
    </div>

<?php } ?>

</div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-head">
                    <h5>Quick Actions</h5>
                </div>

                <div class="quick-list">
                    <a href="products.php"><i class="bi bi-plus-circle"></i> Add a product</a>
                    <a href="#"><i class="bi bi-megaphone"></i> Create a promotion</a>
                    <a href="#"><i class="bi bi-person-plus"></i> Add staff member</a>
                    <a href="#"><i class="bi bi-star"></i> Review feedback</a>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
 const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebarBackdrop');
const toggleBtn = document.getElementById('sidebarToggle');

function openSidebar(){
    sidebar.classList.add('show');
    backdrop.classList.add('show');
    document.body.classList.add('sidebar-open');
    toggleBtn.classList.add('hide-toggle');   // class, not inline style
}

function closeSidebar(){
    sidebar.classList.remove('show');
    backdrop.classList.remove('show');
    document.body.classList.remove('sidebar-open');
    toggleBtn.classList.remove('hide-toggle');
}

toggleBtn.addEventListener('click', () => {
    sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
});

backdrop.addEventListener('click', closeSidebar);

window.addEventListener('resize', () => {
    if (window.innerWidth >= 992) {
        closeSidebar();   // this also removes 'hide-toggle', so class state is always clean
    }
});

    // Day / night mode
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlEl = document.documentElement;

    function setIcon(theme){
        themeIcon.classList.remove('bi-moon-stars', 'bi-sun');
        themeIcon.classList.add(theme === 'dark' ? 'bi-sun' : 'bi-moon-stars');
    }

    setIcon(htmlEl.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');

    themeToggle.addEventListener('click', () => {
        const isDark = htmlEl.getAttribute('data-theme') === 'dark';
        const next = isDark ? 'light' : 'dark';

        if(next === 'dark'){
            htmlEl.setAttribute('data-theme', 'dark');
        } else {
            htmlEl.removeAttribute('data-theme');
        }

        localStorage.setItem('cafe-theme', next);
        setIcon(next);
    });
</script>

</body>
</html>