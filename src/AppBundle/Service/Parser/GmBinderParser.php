<?php

namespace AppBundle\Service\Parser;

use AppBundle\Model\Book\Page;
use Phplrt\Compiler\Compiler;
use Phplrt\Source\File;

class GmBinderParser
{
  private $rootDir;

  public function __construct ($rootDir)
  {
    $this->rootDir = $rootDir;
  }

  public function parse ($content)
  {
    $compiler = new Compiler();
    $compiler->load(File::fromPathname(sprintf('%s/../src/AppBundle/Resources/gmbinder.pp2', $this->rootDir)));

    dump($compiler->parse('{ key: "value" }'));die;
  }
}