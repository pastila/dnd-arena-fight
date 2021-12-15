<?php

namespace AppBundle\Model\Fight;

use AppBundle\Exception\CriticalHit;
use AppBundle\Exception\Lose;
use AppBundle\Exception\Miss;
use AppBundle\Exception\Win;
use AppBundle\Model\Character\Barbarian;
use AppBundle\Model\Character\Character;
use AppBundle\Model\Character\Monk;
use AppBundle\Model\Character\Race\HalfOrc;
use AppBundle\Model\Character\Rogue;
use AppBundle\Model\Weapon\Weapon;

class Duel
{
  /**
   * @var Character
   */
  private $firstCharacter;

  /**
   * @var Character
   */
  private $secondCharacter;

  private $step=1;

  public function __construct (Character $firstCharacter, Character $secondCharacter)
  {
    $this->firstCharacter = $firstCharacter;
    $this->secondCharacter = $secondCharacter;
  }

  private function showInfo (Character $character)
  {
    echo sprintf('%s: %s хп, %s кд'.PHP_EOL, $character->getName(), $character->getHp(), $character->getArmor());
    $bonus = $character->getFirstWeapon()->getDamageBonus() + $this->getWeaponPrimaryCharacteristicValue($character, $character->getFirstWeapon());
    $hitBonus = $character->getFirstWeapon()->getHitBonus() + $character->getProficiencyBonus() + $this->getWeaponPrimaryCharacteristicValue($character, $character->getFirstWeapon());
    echo sprintf('Оружие: %sd%s(+%s). Попадание +%s'.PHP_EOL, $character->getFirstWeapon()->getNbDices(), $character->getFirstWeapon()->getDice(), $bonus, $hitBonus);

    if ($character->getSecondWeapon())
    {
      $bonus = (int)$character->getSecondWeapon()->getDamageBonus();
      $hitBonus = $character->getSecondWeapon()->getHitBonus() + $character->getProficiencyBonus() + $this->getWeaponPrimaryCharacteristicValue($character, $character->getSecondWeapon());
      echo sprintf('Второе Оружие: %sd%s(+%s). Попадание +%s'.PHP_EOL, $character->getSecondWeapon()->getNbDices(), $character->getSecondWeapon()->getDice(), $bonus, $hitBonus);
    }
  }

  public function fight ()
  {
    echo 'Бойцы:'.PHP_EOL;
    $this->showInfo($this->firstCharacter);
    echo PHP_EOL;
    $this->showInfo($this->secondCharacter);
    echo PHP_EOL;

    $this->rollInitiative();
    echo sprintf('Бой начинает %s', $this->firstCharacter->getName(). PHP_EOL.PHP_EOL);

    while(true)
    {
      $this->step();
    }
  }

  private function rollInitiative ()
  {
    $first = rand(1, 20) + $this->firstCharacter->getAgility();
    $second = rand(1, 20) + $this->secondCharacter->getAgility();

    echo sprintf('%s роляет %s инициативы'.PHP_EOL, $this->firstCharacter->getName(), $first);
    echo sprintf('%s роляет %s инициативы'.PHP_EOL, $this->secondCharacter->getName(), $second);

    if ($second > $first)
    {
      $tmp = $this->firstCharacter;
      $this->firstCharacter = $this->secondCharacter;
      $this->secondCharacter = $tmp;
    }
  }

  private function resetCharacter (Character $character)
  {
    $character->setHasReaction(true);
    $character->setHasAction(true);
    $character->setHasBonusAction(true);

    if ($character instanceof Rogue)
    {
      $character->setAdvantage(false);
    }
  }

  private function step ()
  {
    echo '---==Round '. $this->step.'==---'.PHP_EOL;
    echo sprintf('%s: %s hp'.PHP_EOL, $this->firstCharacter->getName(), $this->firstCharacter->getHp());
    echo sprintf('%s: %s hp'.PHP_EOL.PHP_EOL, $this->secondCharacter->getName(), $this->secondCharacter->getHp());
    //Reset chars
    $this->resetCharacter($this->firstCharacter);
    $this->resetCharacter($this->secondCharacter);

    $this->round($this->firstCharacter, $this->secondCharacter, true);
    echo PHP_EOL;
    $this->round($this->secondCharacter, $this->firstCharacter);

    $this->step++;
    echo PHP_EOL;
    echo PHP_EOL;
  }

  private function round (Character $char, Character $enemy, $first=false)
  {
    echo sprintf('%s атакует %s'.PHP_EOL, $char->getName(), $enemy->getName());

    if ($char->isEvasion())
    {
      $char->setEvasion(false);
    }

    if ($char instanceof Barbarian && !$char->isHasRage())
    {
      $char->setHasRage(true);
      $char->setHasBonusAction(false);
      echo sprintf('%s входит в ярость'.PHP_EOL, $char->getName());
    }

    $this->attack($char, $enemy, $char->getFirstWeapon(), $first);

    if ($char->isHasDoubleAttack())
    {
      echo sprintf('%s имеет дополнительную атаку и атакует еще раз'.PHP_EOL, $char->getName());
      $this->attack($char, $enemy, $char->getFirstWeapon());
    }

    $char->setHasAction(false);

    if ($char instanceof Barbarian && $char->isHasFury() && $char->isHasBonusAction())
    {
      echo sprintf('%s имеет дополнительную атаку от бешенства и атакует еще раз'.PHP_EOL, $char->getName());
      $this->attack($char, $enemy, $char->getFirstWeapon());
    }

//    if ($char instanceof Monk && $char->isHasBonusAction())
//    {
//      echo sprintf('%s использует уклонение бонусным'.PHP_EOL, $char->getName());
//      $char->setEvasion(true);
//      $char->setNbCi($char->getNbCi() - 1);
//      $char->setHasBonusAction(false);
//    }

    if ($char instanceof Monk && $char->isHasBonusAction())
    {
      if ($char->getNbCi() > 0)
      {
        echo sprintf('%s использует шквал ударов'.PHP_EOL, $char->getName());
        $this->attack($char, $enemy, $char->getFirstWeapon());
        $this->attack($char, $enemy, $char->getFirstWeapon());
        $char->setNbCi($char->getNbCi() - 1);
      }
      else
      {
        echo sprintf('%s использует боевые искусства для дополнительной атаки'.PHP_EOL, $char->getName());
        $this->attack($char, $enemy, $char->getFirstWeapon());
      }

      $char->setHasBonusAction(false);
    }

    if ($char->isHasBonusAction() && $char->getSecondWeapon())
    {
      echo sprintf('%s атакует вторым оружием %s'.PHP_EOL, $char->getName(), $enemy->getName());
      $this->attack($char, $enemy, $char->getSecondWeapon());
      $char->setHasBonusAction(false);
    }

    if ($char instanceof Barbarian && !$char->isHasFury())
    {
      $char->setHasFury(true);
      echo sprintf('%s входит в бешенство'.PHP_EOL, $char->getName());
    }
  }

  /**
   * @param Character $char
   * @param Character $enemy
   * @param $first bool первая атака в раунде
   * @return void
   * @throws Win
   */
  private function attack (Character $char, Character $enemy, Weapon $weapon, $first=false)
  {
    $dmg = null;

    try
    {
      $hit = $this->hit($char, $weapon);

      if ($char instanceof Rogue && $this->step === 1 && $first)
      {
        $hit = max($hit, $this->hit($char, $weapon));
        echo sprintf('%s кидает на попадание с преимуществом...', $char->getName());
        $char->setAdvantage(true);
      }
      elseif ($enemy->isEvasion())
      {
        $hit = min($hit, $this->hit($char, $weapon));
        echo sprintf('%s кидает на попадание с помехой...', $char->getName());
      }
      else
      {
        echo sprintf('%s кидает на попадание..', $char->getName());
      }

      echo sprintf('%s '.PHP_EOL, $hit);

      if ($hit < $enemy->getArmor())
      {
        throw new Miss();
      }

      $dmg = $this->damage($char, $weapon, false, $char->getSecondWeapon() === $weapon);
    }
    catch (CriticalHit $crit)
    {
      echo sprintf('КРИТ!'.PHP_EOL, $char->getName());
      $dmg = $this->damage($char, $weapon, true, $char->getSecondWeapon() === $weapon);
    }
    catch (Miss $miss)
    {
      echo sprintf('Промах!'.PHP_EOL, $char->getName());
    }

    if ($dmg)
    {
      echo sprintf('%s наносит %s урона'.PHP_EOL, $char->getName(), $dmg);

      try
      {
        $this->dealDamage($enemy, $char->getFirstWeapon(), $dmg);
      }
      catch (Lose $lose)
      {
        echo sprintf('%s теряет сознание'.PHP_EOL, $enemy->getName());
        throw new Win($char);
      }
    }
  }

  private function dealDamage (Character $character, Weapon $weapon, $dmg)
  {
    if ($character instanceof Barbarian && $character->isHasRage() && $weapon->getDamageType() === Weapon::DAMAGE_TYPE_PHYSICAL)
    {
      $dmg = ceil($dmg / 2);
      echo sprintf('%s срезает половину урона в ярости'.PHP_EOL, $character->getName());
    }

    if ($character instanceof Rogue && $character->isHasReaction())
    {
      $dmg = ceil($dmg / 2);
      $character->setHasReaction(false);
      echo sprintf('%s срезает половину урона невероятным уклонением'.PHP_EOL, $character->getName());
    }

    echo sprintf('%s получает %s урона'.PHP_EOL, $character->getName(), $dmg);
    $character->setHp($character->getHp() - $dmg);

    if ($character->getHp() <= 0)
    {
      throw new Lose();
    }
  }

  /**
   * @param Character $character
   * @param Weapon $weapon
   * @param bool $critical
   * @param bool $secondHand
   * @return int
   */
  private function damage (Character $character, Weapon $weapon, $critical=false, $secondHand=false)
  {
    echo sprintf('Считаем урон... ');
    $dmg = 0;

    for ($i = 0; $i < $weapon->getNbDices(); $i++)
    {
      $d = rand(1, $weapon->getDice());
      //урон с кубов
      $dmg += $d;
      echo sprintf(' + %s кубик', $d);
    }

    if ($weapon->getDamageBonus())
    {
      //бонус оружия, если есть
      $dmg += $weapon->getDamageBonus();
      echo sprintf(' + %s бонус оружия ', $weapon->getDamageBonus());
    }

    if (!$secondHand)
    {
      //бонус характеристики считается только на тычку с основной атаки, если нет фити
      $dmg += $this->getWeaponPrimaryCharacteristicValue($character, $weapon);
      echo sprintf(' + %s модификатор характеристики', $this->getWeaponPrimaryCharacteristicValue($character, $weapon));
    }

    if ($critical)
    {
      for ($i = 0; $i < $weapon->getNbDices(); $i++)
      {
        $d = rand(1, $weapon->getDice());
        //добавляем еще все кубы оружия при крите
        $dmg += $d;
        echo sprintf(' + %s с крита', $d);
      }

      if ($character instanceof HalfOrc)
      {
        $d = rand(1, $weapon->getDice());
        //полуорк добавляет еще одну кость оружия
        $dmg += $d;
        echo sprintf(' + %s с крита полуорочья', $d);
      }
    }

    if ($character instanceof Barbarian && $character->isHasRage())
    {
      //бонус от ярости
      $dmg += 2;
      echo sprintf(' + 2 от ярости');
    }

    if ($character instanceof Rogue && $character->isAdvantage())
    {
      $d = rand(1, 6) + rand(1, 6) + rand(1, 6);
      //скрытая атака от преимущества
      $dmg += $d;
      echo sprintf('+ %s от скрытой атаки', $d);
    }

    echo PHP_EOL;

    return $dmg;
  }

  private function getWeaponPrimaryCharacteristicValue (Character $character, Weapon $weapon)
  {
    $characteristicBonus = $character->getStrength();

    if ($weapon->isFencing())
    {
      $characteristicBonus = max($characteristicBonus, $character->getAgility());
    }

    return $characteristicBonus;
  }

  /**
   * Учитывает только милишное оружие и считает что персонаж владеет оружием
   * @param Character $character
   * @param Weapon $weapon
   * @return int
   * @throws CriticalHit
   */
  private function hit (Character $character, Weapon $weapon)
  {
    $proficiencyBonus = $character->getProficiencyBonus();
    $characteristicBonus = $this->getWeaponPrimaryCharacteristicValue($character, $weapon);

    $res = rand(1, 20);

    if ($res === 20)
    {
      throw new CriticalHit();
    }

    return $res + $proficiencyBonus + $characteristicBonus + $weapon->getHitBonus();
  }
}