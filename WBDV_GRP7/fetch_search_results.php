<?php
$books = array(
    "The Things You Can See Only When You Slow Down" => "Book1.php",
    "Atomic Habits" => "Book2.php",
    "The Subtle Art of Not Giving a F*ck" => "Book3.php",
    "The Mountain Is You" => "Book4.php",
    "A Gentle Reminder" => "Book5.php",
    "The Strength In Our Scars" => "Book6.php",
    "You're Not Enough (and That's Okay)" => "Book7.php",
    "How to Win Friends & Influence People" => "Book8.php",
    "When You're Ready, This Is How You Heal" => "Book9.php"
);

if (isset($_POST['query'])) {
    $search = strtolower($_POST['query']);
    $results = array();

    foreach ($books as $title => $page) {
        if (strpos(strtolower($title), $search) !== false) {
            $results[$title] = $page;
        }
    }

    if (count($results) > 0) {
        echo '<div class="search-results-container">';
        foreach ($results as $title => $page) {
            echo '<div class="search-result"><a href="' . $page . '">' . $title . '</a></div>';
        }
        echo '</div>';
    } else {
        echo '<p>No results found</p>';
    }
}
?>
