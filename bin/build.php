<?php

use Cocur\Slugify\Slugify;
use Symfony\Component\Filesystem\Filesystem;

require __DIR__ . '/../vendor/autoload.php';

$twigLoader = new Twig\Loader\FilesystemLoader([__DIR__ . '/../resources']);
$twig = new Twig_Environment($twigLoader);
$slugger = new Slugify();
$filesystem = new Filesystem();

$outputPath = __DIR__ .'/../build/';

$filesystem->remove($outputPath . '/*');

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
    'page' => $slugger->slugify($page),
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

  file_put_contents($outputPath . '/' . $slugger->slugify($page) . '.html', $template);
}

$template = $twig->render(
  'transport.html.twig',
  [
    'navigationLinks' => $navigationLinks,
  ]
);
file_put_contents($outputPath . '/index.html', $template);

$filesystem->mirror(dirname(__DIR__) . '/assets', $outputPath . '/assets');
