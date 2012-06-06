<?php
namespace Library\Crypto;

class Blowfish {

  public function randomSalt($digitCost = 11){
    if (!CRYPT_BLOWFISH){
      throw new \RuntimeException("Blowfish not available");
    }

    if (!is_int($digitCost)){
      throw new \RuntimeException("Digit cost must be an integer");
    }

    if ($digitCost < 4 || $digitCost > 31){
      throw new \RuntimeException("Digit cost must be in the range 4-31");
    }

    $digitCost = sprintf('%02d', $digitCost);

    $blowfishPrefix = '$2a$';

    $saltLength = 22;
    $saltAlphabet = array_merge(
      array('.', '/'),
      range(0, 9),
      range('A', 'Z'),
      range('a', 'z')
    );

    $randomChar = function() use($saltAlphabet){
      return $saltAlphabet[array_rand($saltAlphabet)];
    };

    $salt = '';
    for ($i = 0; $i < $saltLength; ++$i){
      $salt .= $randomChar();
    }

    return $blowfishPrefix.$digitCost.'$'.$salt;
  }
}

