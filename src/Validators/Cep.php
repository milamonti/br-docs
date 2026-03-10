<?php

namespace Validation\BrDocs\Validators;

use Validation\BrDocs\Contracts\DocumentValidatorInterface;

final class Cep implements DocumentValidatorInterface
{
  /**
   * Valida um CEP brasileiro.
   * O CEP deve ter 8 dígitos e não pode ser composto apenas de zeros.
   *
   * @param string $value CEP com ou sem formatação
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $cep = self::sanitize($value);

    if (strlen($cep) !== 8) {
      return false;
    }

    if (!ctype_digit($cep)) {
      return false;
    }

    // CEP não pode ser 00000000
    if ($cep === '00000000') {
      return false;
    }

    // O primeiro dígito deve estar entre 0 e 9 (faixa válida de regiões)
    $firstDigit = (int) $cep[0];
    if ($firstDigit < 0 || $firstDigit > 9) {
      return false;
    }

    return true;
  }

  /**
   * Formata um CEP: 00000-000
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $cep = self::sanitize($value);

    if (strlen($cep) !== 8) {
      return $value;
    }

    return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
  }

  /**
   * Remove a formatação do CEP.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Gera um CEP aleatório válido.
   *
   * @param bool $formatted Se deve retornar formatado
   * @return string
   */
  public static function generate(bool $formatted = false): string
  {
    $cep = (string) mt_rand(1, 9);
    for ($i = 0; $i < 7; $i++) {
      $cep .= mt_rand(0, 9);
    }

    return $formatted ? self::format($cep) : $cep;
  }

  /**
   * Retorna a região do CEP.
   *
   * @param string $value
   * @return string|null
   */
  public static function getRegion(string $value): ?string
  {
    $cep = self::sanitize($value);

    if (!self::validate($cep)) {
      return null;
    }

    $regions = [
      '0' => 'Grande São Paulo',
      '1' => 'Interior de São Paulo',
      '2' => 'Rio de Janeiro e Espírito Santo',
      '3' => 'Minas Gerais',
      '4' => 'Bahia e Sergipe',
      '5' => 'Pernambuco, Alagoas, Paraíba e Rio Grande do Norte',
      '6' => 'Ceará, Piauí, Maranhão, Pará, Amazonas, Acre, Amapá e Roraima',
      '7' => 'Distrito Federal, Goiás, Tocantins, Mato Grosso, Mato Grosso do Sul e Rondônia',
      '8' => 'Paraná e Santa Catarina',
      '9' => 'Rio Grande do Sul',
    ];

    return $regions[$cep[0]] ?? null;
  }
}
