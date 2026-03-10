<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class Renavam implements DocumentValidatorInterface
{
  /**
   * Valida um RENAVAM.
   *
   * @param string $value RENAVAM (11 dígitos)
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $renavam = self::sanitize($value);

    // Preenche com zeros à esquerda até 11 dígitos
    $renavam = str_pad($renavam, 11, '0', STR_PAD_LEFT);

    if (strlen($renavam) !== 11) {
      return false;
    }

    if (!ctype_digit($renavam)) {
      return false;
    }

    // Rejeita sequências de dígitos iguais
    if (preg_match('/^(\d)\1{10}$/', $renavam)) {
      return false;
    }

    // Calcula o dígito verificador
    $weights = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
      $sum += (int) $renavam[$i] * $weights[$i];
    }

    $checkDigit = ($sum * 10) % 11;
    if ($checkDigit >= 10) {
      $checkDigit = 0;
    }

    return (int) $renavam[10] === $checkDigit;
  }

  /**
   * Formata um RENAVAM (sem formato padrão com separadores).
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $renavam = self::sanitize($value);
    return str_pad($renavam, 11, '0', STR_PAD_LEFT);
  }

  /**
   * Remove a formatação do RENAVAM.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera um RENAVAM válido aleatório.
   *
   * @param bool $formatted
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $renavam = '';
    for ($i = 0; $i < 10; $i++) {
      $renavam .= mt_rand(0, 9);
    }

    $weights = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
      $sum += (int) $renavam[$i] * $weights[$i];
    }

    $checkDigit = ($sum * 10) % 11;
    if ($checkDigit >= 10) {
      $checkDigit = 0;
    }

    $renavam .= $checkDigit;

    return $renavam;
  }
}
