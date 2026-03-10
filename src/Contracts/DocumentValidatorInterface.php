<?php

namespace Validation\BrDocs\Contracts;

interface DocumentValidatorInterface
{
  /**
   * Valida o documento.
   *
   * @param string $value
   * @return bool
   */
  public static function validate(string $value): bool;

  /**
   * Formata o documento.
   *
   * @param string $value
   * @return string
   */
  public static function format(string $value): string;

  /**
   * Remove a formatação do documento.
   *
   * @param string $value
   * @return string
   */
  public static function sanitize(string $value): string;

  /**
   * Gera um documento válido aleatório.
   *
   * @param bool $formatted
   * @return string
   */
  public static function generate(bool $formatted = false): string;
}
