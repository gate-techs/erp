<?php
$svg = file_get_contents('d:/xamp8.2/htdocs/gate_tech/erp/Modules/EmployeePortal/files/Frame 1.svg');
if (preg_match('/xlink:href="data:image\/png;base64,([^"]+)"/', $svg, $m)) {
    file_put_contents('d:/xamp8.2/htdocs/gate_tech/erp/public/assets/portal/astronaut.png', base64_decode($m[1]));
    echo "Extracted astronaut.png (" . filesize('d:/xamp8.2/htdocs/gate_tech/erp/public/assets/portal/astronaut.png') . " bytes)\n";
} else {
    echo "Image not found\n";
}
