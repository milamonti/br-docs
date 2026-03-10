<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class Telefone implements DocumentValidatorInterface
{
  /**
   * Valida um número de telefone brasileiro.
   * Aceita fixo (10 dígitos) e celular (11 dígitos), com ou sem DDI.
   *
   * @param string $value
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $phone = self::sanitize($value);

    // Remove DDI +55 se presente
    if (strlen($phone) === 13 && substr($phone, 0, 2) === '55') {
      $phone = substr($phone, 2);
    } elseif (strlen($phone) === 12 && substr($phone, 0, 2) === '55') {
      $phone = substr($phone, 2);
    }

    // Telefone fixo: 10 dígitos (DDD + 8 dígitos)
    // Celular: 11 dígitos (DDD + 9 + 8 dígitos)
    if (strlen($phone) !== 10 && strlen($phone) !== 11) {
      return false;
    }

    if (!ctype_digit($phone)) {
      return false;
    }

    // Valida DDD (primeiros 2 dígitos)
    $ddd = (int) substr($phone, 0, 2);
    $validDdds = [
      11,
      12,
      13,
      14,
      15,
      16,
      17,
      18,
      19, // SP
      21,
      22,
      24, // RJ
      27,
      28, // ES
      31,
      32,
      33,
      34,
      35,
      37,
      38, // MG
      41,
      42,
      43,
      44,
      45,
      46, // PR
      47,
      48,
      49, // SC
      51,
      53,
      54,
      55, // RS
      61, // DF
      62,
      64, // GO
      63, // TO
      65,
      66, // MT
      67, // MS
      68, // AC
      69, // RO
      71,
      73,
      74,
      75,
      77, // BA
      79, // SE
      81,
      82,
      83,
      84,
      85,
      86,
      87,
      88,
      89, // Nordeste
      91,
      92,
      93,
      94,
      95,
      96,
      97,
      98,
      99, // Norte/Nordeste
    ];

    if (!in_array($ddd, $validDdds)) {
      return false;
    }

    // Para celular (11 dígitos), o 3º dígito deve ser 9
    if (strlen($phone) === 11 && $phone[2] !== '9') {
      return false;
    }

    // Para fixo (10 dígitos), o 3º dígito deve ser entre 2 e 5
    if (strlen($phone) === 10) {
      $thirdDigit = (int) $phone[2];
      if ($thirdDigit < 2 || $thirdDigit > 5) {
        return false;
      }
    }

    return true;
  }

  /**
   * Formata um telefone brasileiro.
   * Fixo: (00) 0000-0000
   * Celular: (00) 00000-0000
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $phone = self::sanitize($value);

    // Remove DDI se presente
    if (strlen($phone) === 13 && substr($phone, 0, 2) === '55') {
      $phone = substr($phone, 2);
    }

    if (strlen($phone) === 11) {
      return '(' . substr($phone, 0, 2) . ') ' .
        substr($phone, 2, 5) . '-' .
        substr($phone, 7, 4);
    }

    if (strlen($phone) === 10) {
      return '(' . substr($phone, 0, 2) . ') ' .
        substr($phone, 2, 4) . '-' .
        substr($phone, 6, 4);
    }

    return $value;
  }

  /**
   * Remove a formatação do telefone.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera um número de telefone celular válido aleatório.
   *
   * @param bool $formatted Se deve retornar formatado
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $validDdds = [11, 21, 31, 41, 51, 61, 71, 81, 91];
    $ddd = $validDdds[array_rand($validDdds)];

    // Gera celular (9 + 8 dígitos)
    $phone = (string) $ddd . '9';
    for ($i = 0; $i < 8; $i++) {
      $phone .= mt_rand(0, 9);
    }

    return $formatted ? self::format($phone) : $phone;
  }

  /**
   * Verifica se o telefone é celular.
   *
   * @param string $value
   * @return bool
   */
  public static function isCellphone(string $value): bool
  {
    $phone = self::sanitize($value);

    if (strlen($phone) === 13 && substr($phone, 0, 2) === '55') {
      $phone = substr($phone, 2);
    }

    return strlen($phone) === 11 && $phone[2] === '9';
  }

  /**
   * Verifica se o telefone é fixo.
   *
   * @param string $value
   * @return bool
   */
  public static function isLandline(string $value): bool
  {
    $phone = self::sanitize($value);

    if (strlen($phone) === 12 && substr($phone, 0, 2) === '55') {
      $phone = substr($phone, 2);
    }

    return strlen($phone) === 10;
  }
}
