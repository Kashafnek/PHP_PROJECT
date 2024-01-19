<style>
        /* Ensure border-box is used */
html {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
  }
  
  *,
  *:before,
  *:after {
    -webkit-box-sizing: inherit;
    -moz-box-sizing: inherit;
    box-sizing: inherit;
  }

/*--------------------------------------*/
/*            Core Styles               */
/*--------------------------------------*/

body {
    margin: 0;
    padding:0;
    background: #f2f2f2;
    font-family: 'Montserrat', sans-serif;
    min-width: 198px;
}


/*--------------------------------------*/
/*    Contains Header, Nav, & Logo      */
/*--------------------------------------*/

.box {
    width: 95%; 
    max-width: 1200px;
    margin: 0 auto;
}


/*--------------------------------------*/
/* HEADER: Position relative to help... */
/*    ... poitioning elements absolute  */
/*--------------------------------------*/

header {
    background-color: #4d4d4d;
    padding: 1em 0;
    position: relative;
}

/*------------------------------------------*/
/* Allows multiple floats in same element   */
/*------------------------------------------*/

header::after {
    content: '';
    clear: both;
    display: block;
}

/*--------------------------------------*/
/*            Navigation                */
/*--------------------------------------*/
.site-nav {
    position: absolute;
    top: 100%;
    right: 0%;
    background: #a6a6a6;
    height: 0px;
    overflow: hidden;
}

.site-nav-open {
    height: auto;
}

.site-nav ul {
    margin: 0;
    padding: 0;
    list-style: none;  /* Removes li bullets */
}

.site-nav li {
    border-bottom: 1px solid #8c8c8c;  /* border beneath nav li */
}

.site-nav li:last-child {     /* removes border from last nav li */
    border: none;
}

.site-nav a {            /* Displays nav links as block for smaller screens */
    color: #f2f2f2;
    display: block;
    padding: 2em 2em 2em 1.5em;  /* padding for links */
    text-transform: uppercase;
    text-decoration: none;
}

.site-nav a:hover,         /* hover is for computers and focus is for mobile */
.site-nav a:focus {
    background: #00b3b3;
    color: #4d4d4d;
}

/*--------------------------------------*/
/*   ICON styling for smaller screens   */
/*--------------------------------------*/ 

.site-nav-icon  {
    display: inline-block;   /* allows margin changes */
    font-size: 1.5em;
    margin-right: 1em;
    width: 1em;           /* width lines the icons up */
    text-align: right;  
    color: rgba(255, 255, 255, .35);  /* white with low opacity to make icons less bright */
}

/*--------------------------------------*/
/* Acts as btn & contains hamburger     */
/*--------------------------------------*/

.menu-toggle {
    padding: 1em;
    position: absolute;
    top: 1.7em;
    right: 1em;
    cursor: pointer;
}


/*--------------------------------------*/
/*   Styles the hamburger for btn       */
/*--------------------------------------*/

.hamburger,
.hamburger::before,
.hamburger::after {
    content:'';
    display: block;
    background: #00b3b3;
    height: 3px;
    width: 1.75em;
    border-radius: 2px;
    transition: all ease-in-out 500ms;
}

/*--------------------------------------*/
/* Instance of hamburger (top line)     */
/*--------------------------------------*/

.hamburger::before {
    transform: translateY(-7px);
}

/*--------------------------------------*/
/* Instance of hamburger bottom line    */
/*--------------------------------------*/

.hamburger::after {
    transform: translateY(4px);
}

/*--------------------------------------*/
/* When Nav menu is open, hamburger = X */
/*--------------------------------------*/

.open .hamburger {
    transform: rotate(45deg);
    background: #ffc299;
}

/*--------------------------------------*/
/* Before hamburger line is invisible   */
/*--------------------------------------*/
.open .hamburger::before {
    opacity: 0;            
}

/*--------------------------------------*/
/* Animates after line to create an X   */
/*--------------------------------------*/
.open .hamburger::after {
    transform: translateY(-3px) rotate(-90deg);
    background: #ffc299;
}




/*--------------------------------------*/
/*    For Device (Screens > 700px )     */
/*--------------------------------------*/
@media (min-width:700px) {

    .menu-toggle {       /* Removes hamburger menu */
        display: none;
    }

    .site-nav {         /* Positions site nav & removes background color */
        height: auto;
        position: relative;
        background: transparent;
        float: right;
        font-weight: 300;
    }

    .site-nav li {               /* inline-block and removes border from bottom */
        display: inline-block;
        border: none;
    }

    .site-nav a {       /* Adds spacing between Nav links */
        padding: 0;
        margin-left: 3em;
    }

    .site-nav a:hover,      /* Makes link background transparent on hover */
    .site-nav a:focus {        /* Changes font color to light blue */
        background: transparent;
        font-weight: 800;
        color: #00b3b3;
    }

    .site-nav .site-nav-icon {     /* Removes Font Awesome Icons for larger screens */
        display: none;
    }
    
     /* New CSS for social profile card */
     .social-profile-card {
                float: left;
                margin-right: 20px; /* Adjust the margin as needed */
                padding: 20px;
                background-color: #fff;
                margin-top: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .profile-picture {
                width: 200px; /* Adjust the size as needed */
                height: 150px; /* Adjust the size as needed */
                border-radius: 50%;
                overflow: hidden;
                margin-bottom: 10px;
            }

            .profile-picture img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .profile-info {
                text-align: center;
            }

            .profile-info h2 {
                margin: 0;
                font-size: 1.5em;
            }

            .social-links {
                margin-top: 10px;
            }

            .social-links a {
                display: inline-block;
                margin-right: 10px;
                color: #333;
                text-decoration: none;
                font-size: 1.2em;
            }
        }
.site-nav a:nth-child(5),
        .site-nav a:nth-child(6) {
            background: #00b3b3;
            color: #4d4d4d;
        }
        </style>
</head>
<body>
    <header>
        <div class="box" >

            <nav class="site-nav" >
                <ul>
                <li>
                        <a href="post form.php">Home</a>
                    </li>
                <?php
            include("config.php");

              $categoryNameQuery = "SELECT * FROM categories";
              $categoryNameResult = $con->query($categoryNameQuery);

             if (!$categoryNameResult) {
             die("Query failed: " . $con->error);
            }
            

             if ($categoryNameResult->num_rows > 0) {
             while ($category = $categoryNameResult->fetch_assoc()) {
              echo "<li>
             <a href='RealEstate.php?categoryId={$category['category_id']}'>{$category['category_name']}</a>
        </li>";
    }
}
?>
                    <li>
                        <a href="contactform.php">Contact Form</a>
                    </li>
                     <li>
                        <a href="feedbackform.php">Feedback Form</a>
                    </li>
                   
                    <li>
                        <a href="login.php">Log In / Sign Up</a>
                    </li>
                    <li>
                        <a href="log out.php">Log Out</a>
                    </li>
                </ul>
            </nav>
    
            <div class="menu-toggle">
                <div class="hamburger"></div>
            </div>
        </div>
    </header>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.menu-toggle').click(function() {
                $('.site-nav').toggleClass('site-nav-open', 500);
                $(this).toggleClass('open');
            });
        });
    </script>