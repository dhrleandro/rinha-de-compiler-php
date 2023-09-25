<?php

$GLOBALS['sumCall'] = 0;

function sum($a, $b) {
    $GLOBALS['sumCall']++;
    $r = $a + $b;
    if ($r >= 10) {
        return 1;
    } else {
        return sum($a + 1, $b + 1) + sum($a + 1, $b + 1);
    }
}

echo "\n";
echo 'sum: '.sum(4, 4);
echo "\n";
echo "Calls: ".$GLOBALS['sumCall']."\n\n";

