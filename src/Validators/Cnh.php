<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class Cnh implements DocumentValidatorInterface
{
  /**
   * Valida uma CNH (Carteira Nacional de Habilitação).
   *
   * @param string $value CNH (11 dígitos)
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $cnh = self::sanitize($value);

    if (strlen($cnh) !== 11) {
      return false;
    }

    if (!ctype_digit($cnh)) {
      return false;
    }

    // Rejeita CNHs com todos os dígitos iguais
    if (preg_match('/^(\d)\1{10}$/', $cnh)) {
      return false;
    }

    // Calcula o primeiro dígito verificador
    $sum1 = 0;
    for ($i = 0; $i < 9; $i++) {
      $sum1 += (int) $cnh[$i] * (9 - $i);
    }
    $firstDigit = $sum1 % 11;
    if ($firstDigit >= 10) {
      $firstDigit = 0;
    }

    // Calcula o segundo dígito verificador
    $sum2 = 0;
    for ($i = 0; $i < 9; $i++) {
      $sum2 += (int) $cnh[$i] * (1 + $i);
    }
    $secondDigit = $sum2 % 11;
    if ($secondDigit >= 10) {
      $secondDigit = 0;
    }

    return (int) $cnh[9] === $firstDigit && (int) $cnh[10] === $secondDigit;
  }

  /**
   * Formata uma CNH (não possui formato padrão com separadores).
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    return self::sanitize($value);
  }

  /**
   * Remove caracteres não numéricos.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera uma CNH válida aleatória.
   *
   * @param bool $formatted
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $cnh = '';
    for ($i = 0; $i < 9; $i++) {
      $cnh .= mt_rand(0, 9);
    }

    // Calcula primeiro dígito verificador
    $sum1 = 0;
    for ($i = 0; $i < 9; $i++) {
      $sum1 += (int) $cnh[$i] * (9 - $i);
    }
    $firstDigit = $sum1 % 11;
    if ($firstDigit >= 10) {
      $firstDigit = 0;
    }
    $cnh .= $firstDigit;

    // Calcula segundo dígito verificador
    $sum2 = 0;
    for ($i = 0; $i < 9; $i++) {
      $sum2 += (int) $cnh[$i] * (1 + $i);
    }
    $secondDigit = $sum2 % 11;
    if ($secondDigit >= 10) {
      $secondDigit = 0;
    }
    $cnh .= $secondDigit;

    return $cnh;
  }
}
