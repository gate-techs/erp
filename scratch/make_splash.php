<?php

$svgPath = 'd:/xamp8.2/htdocs/gate_tech/erp/Modules/EmployeePortal/files/Frame 1.svg';
$svg = file_get_contents($svgPath);

// Target lines 15-22 to wrap in <g id="orbitSpinner" class="orbit-spinner">
// and lines 23-25 to wrap in <g class="floating-astronaut">

$targetOrbitStart = '<rect x="27.773" y="68.9951"';
$targetAstronautStart = '<g filter="url(#filter6_d_108_19419)">';
$targetDefs = '<defs>';

// Find positions
$posOrbit = strpos($svg, $targetOrbitStart);
$posAstro = strpos($svg, $targetAstronautStart);
$posDefs = strpos($svg, $targetDefs);

if ($posOrbit === false || $posAstro === false || $posDefs === false) {
    die("Could not find targets\n");
}

// Split
$part1 = substr($svg, 0, $posOrbit);
$partOrbit = substr($svg, $posOrbit, $posAstro - $posOrbit);
$partAstro = substr($svg, $posAstro, $posDefs - $posAstro);
$partRest = substr($svg, $posDefs);

$modifiedSvg = $part1 . 
    '<g id="orbitSpinner" class="orbit-spinner">' . "\n" . 
    $partOrbit . 
    '</g>' . "\n" . 
    '<g class="floating-astronaut">' . "\n" . 
    $partAstro . 
    '</g>' . "\n" . 
    $partRest;

file_put_contents('d:/xamp8.2/htdocs/gate_tech/erp/public/assets/portal/animated-splash.svg', $modifiedSvg);
echo "Animated SVG created successfully: " . strlen($modifiedSvg) . " bytes\n";
