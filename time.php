<?php
// Time until _Sraz na marence_ 29.2.2032
function dateDist($format, $endDate, $beginDate) {
    $date_parts1=explode($format, $beginDate);
    $date_parts2=explode($format, $endDate);
    $start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
    $end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
    return $end_date - $start_date;
}

$marenkaUS="02/29/2032";
$marenkaCZ="29/02/2032";
$nowCZ = date("d/m/Y");
$nowUS = date("m/d/Y");

print "Dnes je " .$nowCZ. ".<br/>";
print "Do srazu na Marence ".$marenkaCZ." zbyva " .dateDist("/", $marenkaUS, $nowUS). " dni.";


?>
