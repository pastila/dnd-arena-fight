<?php

namespace AppBundle\Controller;

use AppBundle\Exception\Win;
use AppBundle\Model\Character\Barbarian;
use AppBundle\Model\Character\Monk;
use AppBundle\Model\Character\Race\Elf;
use AppBundle\Model\Character\Race\HalfOrc;
use AppBundle\Model\Character\Race\Human;
use AppBundle\Model\Character\Rogue;
use AppBundle\Model\Fight\Duel;
use AppBundle\Model\Weapon\Weapon;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends Controller
{
  /**
   * @Route("/", name="homepage")
   */
  public function indexAction (Request $request)
  {
    $bar = new Barbarian();
    $axe = new Weapon(2, 6);
    $bar->setFirstWeapon($axe);
    $bar->setName('Grobash');
    $bar->setAgility(2);
    $bar->setStrength(4);
    $bar->setProficiencyBonus(3);
    $bar->setHp(52);
    $bar->setArmor(15);
    $bar->setHasDoubleAttack(true);
    $bar->setRace(new HalfOrc());

    $rogue = new Rogue();
    $sword = new Weapon(1, 6);
    $sword->setFencing(true);
    $igla = new Weapon(1, 4);
    $igla->setDamageBonus(1);
    $igla->setHitBonus(1);
    $igla->setFencing(true);
    $igla->setDamageType(Weapon::DAMAGE_TYPE_MAGIC);
    $rogue->setFirstWeapon($sword);
    $rogue->setSecondWeapon($igla);
    $rogue->setName('Havi');
    $rogue->setAgility(3);
    $rogue->setStrength(1);
    $rogue->setArmor(15);
    $rogue->setHp(41);
    $rogue->setRace(new Human());
    $rogue->setProficiencyBonus(3);

    $monk = new Monk();
    $hand = new Weapon(1, 6);
    $hand->setFencing(true);
    $monk->setFirstWeapon($hand);
    $monk->setName('Darkon');
    $monk->setAgility(3);
    $monk->setStrength(0);
    $monk->setArmor(16);
    $monk->setHp(29);
    $monk->setRace(new Elf());
    $monk->setProficiencyBonus(3);
    $monk->setHasDoubleAttack(true);

    $duel = new Duel($bar, $monk);

    echo '<pre>';

    try
    {
      $duel->fight();
    }
    catch (Win $win)
    {
      echo sprintf('Победил %s с %s хп', $win->getWinner()->getName(), $win->getWinner()->getHp());
    }

    die;


    return $this->render('@App/homepage/index.html.twig');
  }
}
