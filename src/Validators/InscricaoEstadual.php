<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class InscricaoEstadual implements DocumentValidatorInterface
{
  /**
   * Valida uma Inscrição Estadual.
   * A validação é baseada no padrão geral (por UF é muito extenso).
   * Verifica se contém entre 2 e 14 dígitos.
   *
   * @param string $value Inscrição Estadual
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $ie = self::sanitize($value);

    // Inscrição Estadual ISENTO
    if (strtoupper(trim($value)) === 'ISENTO') {
      return true;
    }

    if (strlen($ie) < 2 || strlen($ie) > 14) {
      return false;
    }

    if (!ctype_digit($ie)) {
      return false;
    }

    // Rejeita sequências de dígitos iguais
    if (preg_match('/^(\d)\1+$/', $ie)) {
      return false;
    }

    return true;
  }

  /**
   * Valida Inscrição Estadual por UF específica.
   *
   * @param string $value
   * @param string $uf Sigla da UF (ex: SP, RJ, MG)
   * @return bool
   */
  public static function validateByUf(string $value, string $uf): bool
  {
    $ie = self::sanitize($value);
    $uf = strtoupper(trim($uf));

    $patterns = [
      'AC' => '/^\d{13}$/',
      'AL' => '/^\d{9}$/',
      'AM' => '/^\d{9}$/',
      'AP' => '/^\d{9}$/',
      'BA' => '/^\d{6,8}$/',
      'CE' => '/^\d{9}$/',
      'DF' => '/^\d{13}$/',
      'ES' => '/^\d{9}$/',
      'GO' => '/^\d{9}$/',
      'MA' => '/^\d{9}$/',
      'MG' => '/^\d{13}$/',
      'MS' => '/^\d{9}$/',
      'MT' => '/^\d{11}$/',
      'PA' => '/^\d{9}$/',
      'PB' => '/^\d{9}$/',
      'PE' => '/^\d{14}$/',
      'PI' => '/^\d{9}$/',
      'PR' => '/^\d{10}$/',
      'RJ' => '/^\d{8}$/',
      'RN' => '/^\d{9,10}$/',
      'RO' => '/^\d{14}$/',
      'RR' => '/^\d{9}$/',
      'RS' => '/^\d{10}$/',
      'SC' => '/^\d{9}$/',
      'SE' => '/^\d{9}$/',
      'SP' => '/^\d{12}$/',
      'TO' => '/^\d{11}$/',
    ];

    if (!isset($patterns[$uf])) {
      return false;
    }

    return (bool) preg_match($patterns[$uf], $ie);
  }

  /**
   * Formata uma Inscrição Estadual (sem formato padrão universal).
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    return self::sanitize($value);
  }

  /**
   * Remove a formatação da Inscrição Estadual.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera uma Inscrição Estadual aleatória (genérica, 9 dígitos).
   *
   * @param bool $formatted
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $ie = '';
    for ($i = 0; $i < 9; $i++) {
      $ie .= mt_rand(0, 9);
    }

    return $ie;
  }
}
