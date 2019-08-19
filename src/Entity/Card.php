<?php
namespace App\Entity;

class Card {
    private $card;

    private $suit;

    private $color;

    public function getCard() {
        return $this->card;
    }
    public function setCard($card) {
        $this->card = $card;
    }

    public function getSuit()
    {
        return $this->suit;
    }
    public function setSuit($suit)
    {
        $this->suit = $suit;
    }

    public function getColor()
    {
        return $this->color;
    }
    public function setColor($color)
    {
        $this->color = $color;
    }


}