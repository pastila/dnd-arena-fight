<?php

namespace AppBundle\Model\Character;

class Barbarian extends Character
{
  private $hasRage=false;

  private $hasFury=false;

  /**
   * @return bool
   */
  public function isHasRage ()
  {
    return $this->hasRage;
  }

  /**
   * @param bool $hasRage
   * @return $this
   */
  public function setHasRage ($hasRage)
  {
    $this->hasRage = $hasRage;
    return $this;
  }

  /**
   * @return bool
   */
  public function isHasFury ()
  {
    return $this->hasFury;
  }

  /**
   * @param bool $hasFury
   * @return $this
   */
  public function setHasFury ($hasFury)
  {
    $this->hasFury = $hasFury;
    return $this;
  }
}