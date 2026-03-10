<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class Certidao implements DocumentValidatorInterface
{
  /**
   * Valida uma Certidão de Nascimento, Casamento ou Óbito (novo formato).
   * O formato possui 32 dígitos numéricos.
   *
   * @param string $value
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $certidao = self::sanitize($value);

    if (strlen($certidao) !== 32) {
      return false;
    }

    if (!ctype_digit($certidao)) {
      return false;
    }

    // Tipo de certidão (posições 14-15): 1=nascimento, 2=casamento, 3=óbito e outros
    $tipo = (int) substr($certidao, 14, 1);
    if ($tipo < 1 || $tipo > 8) {
      return false;
    }

    // Calcula o primeiro dígito verificador (posição 30)
    $base = substr($certidao, 0, 30);
    $weights = self::getWeights(30);

    $sum = 0;
    for ($i = 0; $i < 30; $i++) {
      $sum += (int) $base[$i] * $weights[$i];
    }
    $remainder = $sum % 11;
    $digit1 = $remainder > 9 ? 1 : $remainder;

    if ((int) $certidao[30] !== $digit1) {
      return false;
    }

    // Calcula o segundo dígito verificador (posição 31)
    $base2 = substr($certidao, 0, 31);
    $weights2 = self::getWeights(31);

    $sum2 = 0;
    for ($i = 0; $i < 31; $i++) {
      $sum2 += (int) $base2[$i] * $weights2[$i];
    }
    $remainder2 = $sum2 % 11;
    $digit2 = $remainder2 > 9 ? 1 : $remainder2;

    return (int) $certidao[31] === $digit2;
  }

  /**
   * Formata uma certidão: 000000 00 00 0000 0 00000 000 0000000-00
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $c = self::sanitize($value);

    if (strlen($c) !== 32) {
      return $value;
    }

    return substr($c, 0, 6) . ' ' .
      substr($c, 6, 2) . ' ' .
      substr($c, 8, 2) . ' ' .
      substr($c, 10, 4) . ' ' .
      substr($c, 14, 1) . ' ' .
      substr($c, 15, 5) . ' ' .
      substr($c, 20, 3) . ' ' .
      substr($c, 23, 7) . '-' .
      substr($c, 30, 2);
  }

  /**
   * Remove a formatação da certidão.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera uma certidão válida aleatória.
   *
   * @param bool $formatted
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    // Código do cartório (6 dígitos)
    $certidao = str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    // Código do acervo (2 dígitos)
    $certidao .= str_pad((string) mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
    // Código do serviço (2 dígitos)
    $certidao .= str_pad((string) mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
    // Ano do registro (4 dígitos)
    $certidao .= (string) mt_rand(2000, 2025);
    // Tipo (1 dígito: 1=nascimento, 2=casamento, 3=óbito)
    $certidao .= (string) mt_rand(1, 3);
    // Número do livro (5 dígitos)
    $certidao .= str_pad((string) mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    // Número da folha (3 dígitos)
    $certidao .= str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    // Número do termo (7 dígitos)
    $certidao .= str_pad((string) mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

    // Calcula primeiro dígito verificador
    $weights = self::getWeights(30);
    $sum = 0;
    for ($i = 0; $i < 30; $i++) {
      $sum += (int) $certidao[$i] * $weights[$i];
    }
    $remainder = $sum % 11;
    $certidao .= $remainder > 9 ? 1 : $remainder;

    // Calcula segundo dígito verificador
    $weights2 = self::getWeights(31);
    $sum2 = 0;
    for ($i = 0; $i < 31; $i++) {
      $sum2 += (int) $certidao[$i] * $weights2[$i];
    }
    $remainder2 = $sum2 % 11;
    $certidao .= $remainder2 > 9 ? 1 : $remainder2;

    return $formatted ? self::format($certidao) : $certidao;
  }

  /**
   * Retorna os pesos para cálculo do dígito verificador.
   *
   * @param int $length
   * @return array
   */
  private static function getWeights(int $length): array
  {
    $multipliers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    $weights = [];
    for ($i = 0; $i < $length; $i++) {
      $weights[] = $multipliers[$i % 10];
    }

    return $weights;
  }
}
