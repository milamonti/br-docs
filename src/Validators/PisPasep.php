<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class PisPasep implements DocumentValidatorInterface
{
  /**
   * Valida um PIS/PASEP/NIT/NIS.
   *
   * @param string $value PIS com ou sem formatação
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $pis = self::sanitize($value);

    if (strlen($pis) !== 11) {
      return false;
    }

    if (!ctype_digit($pis)) {
      return false;
    }

    // Rejeita PIS com todos os dígitos iguais
    if (preg_match('/^(\d)\1{10}$/', $pis)) {
      return false;
    }

    $weights = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
      $sum += (int) $pis[$i] * $weights[$i];
    }
    $remainder = $sum % 11;
    $digit = $remainder < 2 ? 0 : 11 - $remainder;

    return (int) $pis[10] === $digit;
  }

  /**
   * Formata um PIS/PASEP: 000.00000.00-0
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $pis = self::sanitize($value);

    if (strlen($pis) !== 11) {
      return $value;
    }

    return substr($pis, 0, 3) . '.' .
      substr($pis, 3, 5) . '.' .
      substr($pis, 8, 2) . '-' .
      substr($pis, 10, 1);
  }

  /**
   * Remove a formatação do PIS/PASEP.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera um PIS/PASEP válido aleatório.
   *
   * @param bool $formatted Se deve retornar formatado
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $pis = '';
    for ($i = 0; $i < 10; $i++) {
      $pis .= mt_rand(0, 9);
    }

    $weights = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
      $sum += (int) $pis[$i] * $weights[$i];
    }
    $remainder = $sum % 11;
    $pis .= $remainder < 2 ? 0 : 11 - $remainder;

    return $formatted ? self::format($pis) : $pis;
  }
}
