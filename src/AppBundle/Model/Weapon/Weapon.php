<?php

namespace AppBundle\Model\Weapon;

/**
 *
 */
class Weapon
{
  const DAMAGE_TYPE_PHYSICAL = 1;

  const DAMAGE_TYPE_MAGIC = 2;
  /**
   * @var integer
   */
  private $dice;

  /**
   * @var integer
   */
  private $nbDices;

  /**
   * @var integer
   */
  private $hitBonus;

  /**
   * @var integer
   */
  private $damageBonus;

  /**
   * @var bool
   */
  private $fencing=false;

  private $damageType=self::DAMAGE_TYPE_PHYSICAL;

  public function __construct ($nbDices, $dice)
  {
    $this->nbDices = $nbDices;
    $this->dice = $dice;
  }

  /**
   * @return int
   */
  public function getDice ()
  {
    return $this->dice;
  }

  /**
   * @param int $dice
   * @return $this
   */
  public function setDice ($dice)
  {
    $this->dice = $dice;
    return $this;
  }

  /**
   * @return int
   */
  public function getNbDices ()
  {
    return $this->nbDices;
  }

  /**
   * @param int $nbDices
   * @return $this
   */
  public function setNbDices ($nbDices)
  {
    $this->nbDices = $nbDices;
    return $this;
  }

  /**
   * @return int
   */
  public function getHitBonus ()
  {
    return $this->hitBonus;
  }

  /**
   * @param int $hitBonus
   * @return $this
   */
  public function setHitBonus ($hitBonus)
  {
    $this->hitBonus = $hitBonus;
    return $this;
  }

  /**
   * @return int
   */
  public function getDamageBonus ()
  {
    return $this->damageBonus;
  }

  /**
   * @param int $damageBonus
   * @return $this
   */
  public function setDamageBonus ($damageBonus)
  {
    $this->damageBonus = $damageBonus;
    return $this;
  }

  /**
   * @return bool
   */
  public function isFencing ()
  {
    return $this->fencing;
  }

  /**
   * @param bool $fencing
   * @return $this
   */
  public function setFencing ($fencing)
  {
    $this->fencing = $fencing;
    return $this;
  }

  /**
   * @return int
   */
  public function getDamageType ()
  {
    return $this->damageType;
  }

  /**
   * @param int $damageType
   * @return $this
   */
  public function setDamageType ($damageType)
  {
    $this->damageType = $damageType;
    return $this;
  }
}