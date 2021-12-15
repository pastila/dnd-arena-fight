<?php

namespace AppBundle\Model\Character;

use AppBundle\Model\Character\Race\Race;
use AppBundle\Model\Weapon\Weapon;

/**
 *
 */
class Character
{
  /**
   * @var string
   */
  private $name;

  /**
   * @var integer
   */
  private $hp;

  /**
   * @var integer
   */
  private $armor;

  /**
   * @var integer
   */
  private $proficiencyBonus;

  /**
   * @var integer
   */
  private $agility;

  /**
   * @var integer
   */
  private $strength;

  /**
   * @var Weapon
   */
  private $firstWeapon;

  /**
   * @var Weapon
   */
  private $secondWeapon;

  private $hasAction=true;

  private $hasBonusAction=true;

  private $hasReaction=true;

  private $hasDoubleAttack=false;

  /**
   * @var Race
   */
  private $race;

  /**
   * @var bool
   */
  private $advantage=false;

  private $evasion = false;

  /**
   * @return bool
   */
  public function isAdvantage ()
  {
    return $this->advantage;
  }

  /**
   * @param bool $advantage
   * @return $this
   */
  public function setAdvantage ($advantage)
  {
    $this->advantage = $advantage;
    return $this;
  }

  /**
   * @param bool $evasion
   * @return $this
   */
  public function setEvasion ($evasion)
  {
    $this->evasion = $evasion;
    return $this;
  }

  /**
   * @return bool
   */
  public function isEvasion ()
  {
    return $this->evasion;
  }

  /**
   * @return string
   */
  public function getName ()
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setName ($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return int
   */
  public function getHp ()
  {
    return $this->hp;
  }

  /**
   * @param int $hp
   * @return $this
   */
  public function setHp ($hp)
  {
    $this->hp = $hp;
    return $this;
  }

  /**
   * @return int
   */
  public function getArmor ()
  {
    return $this->armor;
  }

  /**
   * @param int $armor
   * @return $this
   */
  public function setArmor ($armor)
  {
    $this->armor = $armor;
    return $this;
  }

  /**
   * @return int
   */
  public function getProficiencyBonus ()
  {
    return $this->proficiencyBonus;
  }

  /**
   * @param int $proficiencyBonus
   * @return $this
   */
  public function setProficiencyBonus ($proficiencyBonus)
  {
    $this->proficiencyBonus = $proficiencyBonus;
    return $this;
  }

  /**
   * @return int
   */
  public function getAgility ()
  {
    return $this->agility;
  }

  /**
   * @param int $agility
   * @return $this
   */
  public function setAgility ($agility)
  {
    $this->agility = $agility;
    return $this;
  }

  /**
   * @return int
   */
  public function getStrength ()
  {
    return $this->strength;
  }

  /**
   * @param int $strength
   * @return $this
   */
  public function setStrength ($strength)
  {
    $this->strength = $strength;
    return $this;
  }

  /**
   * @return Weapon
   */
  public function getFirstWeapon ()
  {
    return $this->firstWeapon;
  }

  /**
   * @param Weapon $firstWeapon
   * @return $this
   */
  public function setFirstWeapon ($firstWeapon)
  {
    $this->firstWeapon = $firstWeapon;
    return $this;
  }

  /**
   * @return Weapon
   */
  public function getSecondWeapon ()
  {
    return $this->secondWeapon;
  }

  /**
   * @param Weapon $secondWeapon
   * @return $this
   */
  public function setSecondWeapon ($secondWeapon)
  {
    $this->secondWeapon = $secondWeapon;
    return $this;
  }

  /**
   * @return bool
   */
  public function isHasAction ()
  {
    return $this->hasAction;
  }

  /**
   * @param bool $hasAction
   * @return $this
   */
  public function setHasAction ($hasAction)
  {
    $this->hasAction = $hasAction;
    return $this;
  }

  /**
   * @return bool
   */
  public function isHasBonusAction ()
  {
    return $this->hasBonusAction;
  }

  /**
   * @param bool $hasBonusAction
   * @return $this
   */
  public function setHasBonusAction ($hasBonusAction)
  {
    $this->hasBonusAction = $hasBonusAction;
    return $this;
  }

  /**
   * @return bool
   */
  public function isHasReaction ()
  {
    return $this->hasReaction;
  }

  /**
   * @param bool $hasReaction
   * @return $this
   */
  public function setHasReaction ($hasReaction)
  {
    $this->hasReaction = $hasReaction;
    return $this;
  }

  /**
   * @return Race
   */
  public function getRace ()
  {
    return $this->race;
  }

  /**
   * @param Race $race
   * @return $this
   */
  public function setRace ($race)
  {
    $this->race = $race;
    return $this;
  }

  /**
   * @return bool
   */
  public function isHasDoubleAttack ()
  {
    return $this->hasDoubleAttack;
  }

  /**
   * @param bool $hasDoubleAttack
   * @return $this
   */
  public function setHasDoubleAttack ($hasDoubleAttack)
  {
    $this->hasDoubleAttack = $hasDoubleAttack;
    return $this;
  }
}