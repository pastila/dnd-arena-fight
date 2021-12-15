<?php

namespace AppBundle\Exception;


use AppBundle\Model\Character\Character;

class Win extends \Exception
{
  private $winner;

  public function __construct (Character $winner, $message = "", $code = 0, \Throwable $previous = null)
  {
    $this->winner = $winner;
    parent::__construct($message, $code, $previous);
  }

  /**
   * @return Character
   */
  public function getWinner ()
  {
    return $this->winner;
  }
}