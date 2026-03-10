<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class TituloEleitor implements DocumentValidatorInterface
{
  /**
   * Valida um Título de Eleitor.
   *
   * @param string $value Título de Eleitor (12 dígitos)
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $titulo = self::sanitize($value);

    if (strlen($titulo) !== 12) {
      return false;
    }

    if (!ctype_digit($titulo)) {
      return false;
    }

    // Sequência numérica (8 primeiros dígitos)
    $sequencia = substr($titulo, 0, 8);

    // Estado (dígitos 8 e 9 — posições 8 e 9)
    $estado = (int) substr($titulo, 8, 2);

    // O código do estado deve estar entre 01 e 28
    if ($estado < 1 || $estado > 28) {
      return false;
    }

    // Calcula o primeiro dígito verificador
    $weights1 = [2, 3, 4, 5, 6, 7, 8, 9];
    $sum = 0;
    for ($i = 0; $i < 8; $i++) {
      $sum += (int) $sequencia[$i] * $weights1[$i];
    }
    $remainder = $sum % 11;
    if ($remainder === 0) {
      $firstDigit = $estado === 1 || $estado === 2 ? 1 : 0;
    } elseif ($remainder === 1) {
      $firstDigit = 1;
    } else {
      $firstDigit = $remainder;
      // Não se aplica para estados 01 e 02 (SP e MG)
    }

    // Recalcula: o dígito verificador é o resto da divisão por 11
    $sum = 0;
    for ($i = 0; $i < 8; $i++) {
      $sum += (int) $titulo[$i] * $weights1[$i];
    }
    $rest1 = $sum % 11;
    if ($rest1 > 1) {
      $dig1 = $rest1;
    } elseif ($rest1 === 0) {
      $dig1 = ($estado === 1 || $estado === 2) ? 1 : 0;
    } else {
      $dig1 = ($estado === 1 || $estado === 2) ? 1 : 0;
    }

    if ((int) $titulo[10] !== $dig1) {
      return false;
    }

    // Calcula o segundo dígito verificador
    $weights2 = [7, 8, 9];
    $stateStr = substr($titulo, 8, 2);
    $sum2 = ((int) $stateStr[0] * $weights2[0]) +
      ((int) $stateStr[1] * $weights2[1]) +
      ($dig1 * $weights2[2]);

    $rest2 = $sum2 % 11;
    if ($rest2 > 1) {
      $dig2 = $rest2;
    } elseif ($rest2 === 0) {
      $dig2 = ($estado === 1 || $estado === 2) ? 1 : 0;
    } else {
      $dig2 = ($estado === 1 || $estado === 2) ? 1 : 0;
    }

    return (int) $titulo[11] === $dig2;
  }

  /**
   * Formata um Título de Eleitor: 0000 0000 0000
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $titulo = self::sanitize($value);

    if (strlen($titulo) !== 12) {
      return $value;
    }

    return substr($titulo, 0, 4) . ' ' .
      substr($titulo, 4, 4) . ' ' .
      substr($titulo, 8, 4);
  }

  /**
   * Remove a formatação do Título de Eleitor.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera um Título de Eleitor válido aleatório.
   *
   * @param bool $formatted Se deve retornar formatado
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    // Gera sequência de 8 dígitos
    $sequencia = '';
    for ($i = 0; $i < 8; $i++) {
      $sequencia .= mt_rand(0, 9);
    }

    // Gera código do estado (01 a 28)
    $estado = mt_rand(3, 28); // Evita SP(01) e MG(02) para simplificar
    $estadoStr = str_pad((string) $estado, 2, '0', STR_PAD_LEFT);

    // Calcula primeiro dígito verificador
    $weights1 = [2, 3, 4, 5, 6, 7, 8, 9];
    $sum = 0;
    for ($i = 0; $i < 8; $i++) {
      $sum += (int) $sequencia[$i] * $weights1[$i];
    }
    $rest1 = $sum % 11;
    $dig1 = $rest1 > 1 ? $rest1 : 0;

    // Calcula segundo dígito verificador
    $weights2 = [7, 8, 9];
    $sum2 = ((int) $estadoStr[0] * $weights2[0]) +
      ((int) $estadoStr[1] * $weights2[1]) +
      ($dig1 * $weights2[2]);
    $rest2 = $sum2 % 11;
    $dig2 = $rest2 > 1 ? $rest2 : 0;

    $titulo = $sequencia . $estadoStr . $dig1 . $dig2;

    return $formatted ? self::format($titulo) : $titulo;
  }
}
