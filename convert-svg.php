<?php

function convertSVG($inputFilePath, $outputFilePath)
{
  // Load SVG file content
  $svgContent = file_get_contents($inputFilePath);

  if ($svgContent === false) {
    return "Failed to read SVG file.";
  }

  // Remove XML declaration
  $optimizedSVG = preg_replace('/<\?xml.*?\?>/', '', $svgContent);

  // Remove DOCTYPE declaration
  $optimizedSVG = preg_replace('/<!DOCTYPE[^>]*>/', '', $optimizedSVG);

  // Remove grouping tags <g> and </g>
  $optimizedSVG = preg_replace('/<\/?g[^>]*>/', '', $optimizedSVG);

  // Remove the 'version' attribute from the <svg> tag
  $optimizedSVG = preg_replace('/\\s*version="[^"]*"/', '', $optimizedSVG);

  // Remove the 'id' attribute from the <svg> tag
  $optimizedSVG = preg_replace('/\\s*id="[^"]*"/', '', $optimizedSVG);

  // Remove 'xmlns:xlink' attribute from <svg> tag
  $optimizedSVG = preg_replace('/\\s*xmlns:xlink="[^"]*"/', '', $optimizedSVG);

  // Remove 'enable-background' attribute from <svg>v tag
  $optimizedSVG = preg_replace('/\\s*enable-background="[^"]*"/', '', $optimizedSVG);

  // Delete comments
  $optimizedSVG = preg_replace('/<!--.*?-->/', '', $optimizedSVG);

  // Reduce multiple spaces
  $optimizedSVG = preg_replace('/\s{2,}/', ' ', $optimizedSVG);

  // Delete new lines
  $optimizedSVG = preg_replace('/\n/', '', $optimizedSVG);

  // Remove spaces between tags
  $optimizedSVG = preg_replace('/>\s+</', '><', $optimizedSVG);

  // Coordinate compression (limited to 1 decimal place)
  $optimizedSVG = preg_replace_callback('/(\d+\.\d{3,})/', function ($matches) {
    return round($matches[1], 1);
  }, $optimizedSVG);

  // Remove unnecessary whitespace at the beginning and end
  $optimizedSVG = trim($optimizedSVG);

  // Check if the resulting SVG is not empty
  if (empty($optimizedSVG)) {
    return "Optimization resulted in an empty SVG file.";
  }

  // Replace special characters with appropriate URL entities
  $optimizedSVG = rawurlencode($optimizedSVG);

  // Save optimized SVG file
  file_put_contents($outputFilePath, urldecode($optimizedSVG));

  // Prepare the result in CSS format
  $cssCode = "url('data:image/svg+xml,{$optimizedSVG}');";

  return $cssCode;
}

$inputFile = 'example.svg';
$outputFile = 'optimized.svg';

$result = convertSVG($inputFile, $outputFile);
echo $result;
