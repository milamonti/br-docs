<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class Cpf implements DocumentValidatorInterface
{
  /**
   * Valida um CPF.
   *
   * @param string $value CPF com ou sem formatação
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $cpf = self::sanitize($value);

    if (strlen($cpf) !== 11) {
      return false;
    }

    if (!ctype_digit($cpf)) {
      return false;
    }

    // Rejeita CPFs com todos os dígitos iguais
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
      return false;
    }

    // Calcula o primeiro dígito verificador
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
      $sum += (int) $cpf[$i] * (10 - $i);
    }
    $remainder = $sum % 11;
    $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

    if ((int) $cpf[9] !== $digit1) {
      return false;
    }

    // Calcula o segundo dígito verificador
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
      $sum += (int) $cpf[$i] * (11 - $i);
    }
    $remainder = $sum % 11;
    $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

    return (int) $cpf[10] === $digit2;
  }

  /**
   * Formata um CPF: 000.000.000-00
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $cpf = self::sanitize($value);

    if (strlen($cpf) !== 11) {
      return $value;
    }

    return substr($cpf, 0, 3) . '.' .
      substr($cpf, 3, 3) . '.' .
      substr($cpf, 6, 3) . '-' .
      substr($cpf, 9, 2);
  }

  /**
   * Remove a formatação do CPF.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera um CPF válido aleatório.
   *
   * @param bool $formatted Se deve retornar formatado
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $cpf = '';
    for ($i = 0; $i < 9; $i++) {
      $cpf .= mt_rand(0, 9);
    }

    // Calcula primeiro dígito verificador
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
      $sum += (int) $cpf[$i] * (10 - $i);
    }
    $remainder = $sum % 11;
    $cpf .= $remainder < 2 ? 0 : 11 - $remainder;

    // Calcula segundo dígito verificador
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
      $sum += (int) $cpf[$i] * (11 - $i);
    }
    $remainder = $sum % 11;
    $cpf .= $remainder < 2 ? 0 : 11 - $remainder;

    return $formatted ? self::format($cpf) : $cpf;
  }
}
