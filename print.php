<?php
include('vendor/autoload.php');

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML('');
$mpdf->Image('/themes/magazine_lite/images/ticket.PNG', 0, 0, 200, 70, 'jpg', '', true, false);
$mpdf->Output();

?>