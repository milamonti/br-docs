<?php

namespace Validation\BrDocs\Exceptions;

use InvalidArgumentException;

class DocumentException extends InvalidArgumentException
{
  /**
   * Cria exceção para documento inválido.
   *
   * @param string $type Tipo do documento (CPF, CNPJ, etc.)
   * @param string $value Valor informado
   * @return static
   */
  public static function invalid(string $type, string $value): static
  {
    return new static(
      sprintf('O %s informado "%s" é inválido.', strtoupper($type), $value)
    );
  }

  /**
   * Cria exceção para tipo de documento não suportado.
   *
   * @param string $type
   * @return static
   */
  public static function unsupportedType(string $type): static
  {
    return new static(
      sprintf('O tipo de documento "%s" não é suportado.', $type)
    );
  }
}
