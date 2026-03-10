<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class Cnpj implements DocumentValidatorInterface
{
  /**
   * Valida um CNPJ.
   *
   * @param string $value CNPJ com ou sem formatação
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $cnpj = self::sanitize($value);

    if (strlen($cnpj) !== 14) {
      return false;
    }

    if (!ctype_digit($cnpj)) {
      return false;
    }

    // Rejeita CNPJs com todos os dígitos iguais
    if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
      return false;
    }

    // Calcula o primeiro dígito verificador
    $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
      $sum += (int) $cnpj[$i] * $weights1[$i];
    }
    $remainder = $sum % 11;
    $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

    if ((int) $cnpj[12] !== $digit1) {
      return false;
    }

    // Calcula o segundo dígito verificador
    $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 13; $i++) {
      $sum += (int) $cnpj[$i] * $weights2[$i];
    }
    $remainder = $sum % 11;
    $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

    return (int) $cnpj[13] === $digit2;
  }

  /**
   * Formata um CNPJ: 00.000.000/0000-00
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $cnpj = self::sanitize($value);

    if (strlen($cnpj) !== 14) {
      return $value;
    }

    return substr($cnpj, 0, 2) . '.' .
      substr($cnpj, 2, 3) . '.' .
      substr($cnpj, 5, 3) . '/' .
      substr($cnpj, 8, 4) . '-' .
      substr($cnpj, 12, 2);
  }

  /**
   * Remove a formatação do CNPJ.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera um CNPJ válido aleatório.
   *
   * @param bool $formatted Se deve retornar formatado
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $cnpj = '';
    for ($i = 0; $i < 8; $i++) {
      $cnpj .= mt_rand(0, 9);
    }
    $cnpj .= '0001'; // Filial padrão

    // Calcula primeiro dígito verificador
    $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
      $sum += (int) $cnpj[$i] * $weights1[$i];
    }
    $remainder = $sum % 11;
    $cnpj .= $remainder < 2 ? 0 : 11 - $remainder;

    // Calcula segundo dígito verificador
    $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    for ($i = 0; $i < 13; $i++) {
      $sum += (int) $cnpj[$i] * $weights2[$i];
    }
    $remainder = $sum % 11;
    $cnpj .= $remainder < 2 ? 0 : 11 - $remainder;

    return $formatted ? self::format($cnpj) : $cnpj;
  }
}
