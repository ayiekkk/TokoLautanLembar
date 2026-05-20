<?php

$page = isset($_GET['page']) ? $_GET['page'] : "home";

if ($page == "home") {
    include "pages/home.php";
} elseif ($page == "catalog") {
    include "pages/catalog.php";
}
