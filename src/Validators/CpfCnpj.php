<?php

namespace Validation\BrDocs\Validators;

/**
 * Classe para validação de CPF ou CNPJ de forma unificada.
 * Detecta automaticamente o tipo do documento.
 */
final class CpfCnpj
{
  /**
   * Valida CPF ou CNPJ automaticamente.
   *
   * @param string $value CPF ou CNPJ com ou sem formatação
   * @return bool
   */
  public static function validate(string $value): bool
  {
    $document = preg_replace('/\D/', '', $value);

    if (strlen($document) === 11) {
      return Cpf::validate($value);
    }

    if (strlen($document) === 14) {
      return Cnpj::validate($value);
    }

    return false;
  }

  /**
   * Formata CPF ou CNPJ automaticamente.
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string
  {
    $document = preg_replace('/\D/', '', $value);

    if (strlen($document) === 11) {
      return Cpf::format($value);
    }

    if (strlen($document) === 14) {
      return Cnpj::format($value);
    }

    return $value;
  }

  /**
   * Remove a formatação do CPF ou CNPJ.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string
  {
    return preg_replace('/\D/', '', $value);
  }

  /**
   * Detecta o tipo de documento.
   *
   * @param string $value
   * @return string|null 'cpf', 'cnpj' ou null
   */
  public static function detect(string $value): ?string
  {
    $document = preg_replace('/\D/', '', $value);

    if (strlen($document) === 11) {
      return 'cpf';
    }

    if (strlen($document) === 14) {
      return 'cnpj';
    }

    return null;
  }
}
