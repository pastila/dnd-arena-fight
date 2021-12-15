<?php


namespace AppBundle\Service\Import;


use AppBundle\Entity\Spell\Spell;
use AppBundle\Entity\Spell\SpellComponent;
use AppBundle\Entity\Spell\SpellSchool;
use Doctrine\ORM\EntityManagerInterface;

class SpellImport
{
  const ALL_URL = 'https://raw.githubusercontent.com/Etignis/DnD_SpellList_eng_rus/master/spells/allSpells.js';

  private $entityManager;

  public function __construct (EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function importSpells ()
  {
    $data = mb_substr(file_get_contents(self::ALL_URL), 16);
    $data = str_replace(["\r", "\n", "\t"], null, $data);
    file_put_contents('/var/www/var/data.json', $data);

    $parser = new \JsonCollectionParser\Parser();
    $parser->parse('/var/www/var/data.json', function($item){
      $ru = $item['ru'];
      $en = $item['en'];
      $spell = $this->getOrCreateSpellByName($en['name']);
      $spell->setName($ru['name']);
      $spell->setLevel((int)$ru['level']);
      $spell->setText($ru['text']);
      $spell->setOriginalText($en['text']);
      $spell->setMaterials($ru['materials']);
      $spell->setOriginalMaterials($en['materials']);
      $spell->setSource($en['source']);
      $spell->setRitual(isset($en['ritual']));

      $components = explode(',', $en['components']);
      $spellComponents = [];

      foreach ($components as $component)
      {
        $spellComponent = new SpellComponent();
        $spellComponent->setSpell($spell);
        $spellComponent->setName($component);
        $spellComponents[] = $spellComponent;
      }

      $spell->setComponents($spellComponents);

      $school = $this->getOrCreateSchoolByName($en['school']);
      $school->setTitle($ru['school']);
      $spell->setSchool($school);

      $this->resolveCastingType($spell, $en['castingTime']);
      $this->resolveRange($spell, $en['range']);
      $this->resolveDuration($spell, $en['duration']);

      $this->entityManager->persist($spell);
    }, true);

    $this->entityManager->flush();
  }

  private function resolveDuration (Spell $spell, $type)
  {
    if (preg_match('/(\d+) hour/', $type, $m))
    {
      $spell->setDurationType(Spell::DURATION_TIME);
      $spell->setDuration($m[1] * 60 * 60);
    }
    elseif (preg_match('/(\d+) minute/', $type, $m))
    {
      $spell->setDurationType(Spell::DURATION_TIME);
      $spell->setDuration($m[1] * 60);
    }
    elseif (preg_match('/(\d+) day/', $type, $m))
    {
      $spell->setDurationType(Spell::DURATION_TIME);
      $spell->setDuration($m[1] * 60 * 60 * 24);
    }
    elseif (preg_match('/(\d+) round/', $type, $m))
    {
      $spell->setDurationType(Spell::DURATION_ROUND);
      $spell->setDuration($m[1]);
    }
    elseif (preg_match('/Instantaneous/', $type, $m))
    {
      $spell->setDurationType(Spell::DURATION_INSTANT);
      $spell->setDuration(null);
    }
    elseif (preg_match('/Until dispelled/', $type, $m))
    {
      $spell->setDurationType(Spell::DURATION_UNTIL_DISPELLED);
      $spell->setDuration(null);
    }
    else
    {
      throw new \Exception($type);
    }

    if (strpos($type, 'Concentration') !== false)
    {
      $spell->setDurationConcentration(true);
    }
  }

  private function resolveCastingType (Spell $spell, $type)
  {
    if (preg_match('/^(\d+) action$/', $type, $m))
    {
      $spell->setCastingTimeType(Spell::CASTING_TIME_ACTION);
      $spell->setCastingTime($m[1]);
    }
    elseif (preg_match('/^(\d+) minute$/', $type, $m))
    {
      $spell->setCastingTimeType(Spell::CASTING_TIME_TIME);
      $spell->setCastingTime($m[1] * 60);
    }
    elseif (preg_match('/^(\d+) hour$/', $type, $m))
    {
      $spell->setCastingTimeType(Spell::CASTING_TIME_TIME);
      $spell->setCastingTime($m[1] * 60 * 60);
    }
    elseif (preg_match('/^(\d+) reaction$/', $type, $m))
    {
      $spell->setCastingTimeType(Spell::CASTING_TIME_REACTION);
      $spell->setCastingTime($m[1]);
    }
    elseif (preg_match('/^(\d+) bonus action$/', $type, $m))
    {
      $spell->setCastingTimeType(Spell::CASTING_TIME_BONUS_ACTION);
      $spell->setCastingTime($m[1]);
    }
    else
    {
      throw new \Exception($type);
    }
  }

  private function resolveRange (Spell $spell, $type)
  {
    if (preg_match('/^(\d+) feet$/', $type, $m))
    {
      $spell->setRangeType(Spell::RANGE_FEET);
      $spell->setRange($m[1]);
    }
    elseif (preg_match('/^Self/', $type, $m))
    {
      $spell->setCastingTimeType(Spell::RANGE_SELF);
      $spell->setCastingTime(null);
    }
    elseif (preg_match('/^Touch$/', $type, $m))
    {
      $spell->setCastingTimeType(Spell::RANGE_TOUCH);
      $spell->setCastingTime(null);
    }
    else
    {
      throw new \Exception($type);
    }
  }

  /**
   * @param $name
   * @return Spell
   */
  private function getOrCreateSpellByName ($name)
  {
    $spell = $this->entityManager->getRepository('AppBundle:Spell\Spell')->findOneBy(['originalName' => $name]);

    if ($spell === null)
    {
      $spell = new Spell();
      $spell->setOriginalMaterials($name);
    }

    return $spell;
  }

  private function getOrCreateSchoolByName ($name)
  {
    $school = $this->entityManager->getRepository('AppBundle:Spell\SpellSchool')->findOneBy(['originalTitle' => $name]);

    if ($school === null)
    {
      $school = new SpellSchool();
      $school->setOriginalTitle($name);
    }

    return $school;
  }
}