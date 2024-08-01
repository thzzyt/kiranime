<?php

add_action('kiranime_show_ads', 'kiranime_show_ads', 10, 2);
function kiranime_show_ads($location = null, $stricted = false)
{
    if (!$location) {
        echo "";
    }

    $val = get_option($location);
    if (!$val) {
        echo "";
    }

    if (!$stricted) {
        echo "<div class='w-full my-2 sm:my-5'>";
        echo $val;
        echo "</div>";
    } else {
        echo $val;
    }
}
