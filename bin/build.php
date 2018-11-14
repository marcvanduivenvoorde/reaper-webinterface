<?php

use Symfony\Component\Filesystem\Filesystem;

require __DIR__ . '/../vendor/autoload.php';

$twigLoader = new Twig\Loader\FilesystemLoader([__DIR__ . '/../resources']);
$twig = new Twig_Environment($twigLoader);

$outputPath = __DIR__ .'/../build/';

// clear output path

$configuration = json_decode(file_get_contents(dirname(__DIR__) . '/build.json'), true);

if (!$configuration) {
  die('no valid config');
}

$navigationLinks = [
  [
    'page' => 'index',
    'title' => 'transport',
  ],
];


foreach ($configuration['pages'] as $page => $items) {
  $navigationLinks[] = [
    'page' => strtolower($page),
    'title' => $page,
  ];
}

foreach ($configuration['pages'] as $page => $data) {
  $items = [];
  foreach ($data as $index => $row) {
    $items[] = [
      'identifier' => $index,
      'title' => $row,
    ];
  }
  $template = $twig->render(
    'page.html.twig',
    [
      'navigationLinks' => $navigationLinks,
      'items' => $items,
    ]
  );

  file_put_contents($outputPath . '/' . strtolower($page) . '.html', $template);
}

$template = $twig->render(
  'transport.html.twig',
  [
    'navigationLinks' => $navigationLinks
  ]
);
file_put_contents($outputPath . '/index.html', $template);

$filesystem = new Filesystem();

$filesystem->mirror(dirname(__DIR__) . '/assets', $outputPath . '/assets');