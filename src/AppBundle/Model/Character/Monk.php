<?php

namespace AppBundle\Model\Character;

class Monk extends Character
{
  private $nbCi=5;

  /**
   * @return int
   */
  public function getNbCi ()
  {
    return $this->nbCi;
  }

  /**
   * @param int $nbCi
   * @return $this
   */
  public function setNbCi ($nbCi)
  {
    $this->nbCi = $nbCi;
    return $this;
  }
}