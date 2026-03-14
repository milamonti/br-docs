<?php

namespace Validation\BrDocs;

use Validation\BrDocs\Validators\Cep;
use Validation\BrDocs\Validators\Cnh;
use Validation\BrDocs\Validators\Cpf;
use Validation\BrDocs\Validators\Cnpj;
use Validation\BrDocs\Validators\Certidao;
use Validation\BrDocs\Validators\CpfCnpj;
use Validation\BrDocs\Validators\Telefone;
use Validation\BrDocs\Validators\PisPasep;
use Validation\BrDocs\Validators\Renavam;
use Validation\BrDocs\Validators\TituloEleitor;
use Validation\BrDocs\Validators\InscricaoEstadual;
use Validation\BrDocs\Exceptions\DocumentException;

final class BrDocs
{
  /**
   * Mapa de tipos de documentos para suas classes validadoras.
   */
  private const VALIDATORS = [
    "cpf"                => Cpf::class,
    "cnpj"               => Cnpj::class,
    "cpf_cnpj"           => CpfCnpj::class,
    "cpfcnpj"            => CpfCnpj::class,
    "cep"                => Cep::class,
    "cnh"                => Cnh::class,
    "pis"                => PisPasep::class,
    "pasep"              => PisPasep::class,
    "pis_pasep"          => PisPasep::class,
    "nit"                => PisPasep::class,
    "nis"                => PisPasep::class,
    "titulo_eleitor"     => TituloEleitor::class,
    "titulo"             => TituloEleitor::class,
    "inscricao_estadual" => InscricaoEstadual::class,
    "ie"                 => InscricaoEstadual::class,
    "renavam"            => Renavam::class,
    "certidao"           => Certidao::class,
    "telefone"           => Telefone::class,
    "phone"              => Telefone::class,
    "celular"            => Telefone::class,
  ];

  /**
   * Valida um documento brasileiro.
   *
   * @param string $type  Tipo do documento (cpf, cnpj, cep, cnh, pis, titulo, ie, renavam, certidao, telefone)
   * @param string $value Valor do documento
   * @return bool
   *
   * @throws DocumentException Se o tipo não for suportado
   */
  public static function validate(string $type, string $value): bool
  {
    $validator = self::resolveValidator($type);

    return $validator::validate($value);
  }

  /**
   * Alias para validate(). Retorna true se o documento for válido.
   *
   * @param string $type
   * @param string $value
   * @return bool
   */
  public static function isValid(string $type, string $value): bool
  {
    return self::validate($type, $value);
  }

  /**
   * Valida e lança exceção se inválido.
   *
   * @param string $type
   * @param string $value
   * @return true
   *
   * @throws DocumentException Se o documento for inválido
   */
  public static function validateOrFail(string $type, string $value): bool
  {
    if (!self::validate($type, $value)) {
      throw DocumentException::invalid($type, $value);
    }

    return true;
  }

  /**
   * Formata um documento brasileiro.
   *
   * @param string $type
   * @param string $value
   * @return string
   */
  public static function format(string $type, string $value): string
  {
    $validator = self::resolveValidator($type);

    return $validator::format($value);
  }

  /**
   * Remove a formatação de um documento brasileiro.
   *
   * @param string $type
   * @param string $value
   * @return string
   */
  public static function sanitize(string $type, string $value): string
  {
    $validator = self::resolveValidator($type);

    return $validator::sanitize($value);
  }

  /**
   * Gera um documento válido aleatório.
   *
   * @param string $type      Tipo do documento
   * @param bool   $formatted Se deve retornar formatado
   * @return string
   */
  public static function generate(string $type, bool $formatted = false): string
  {
    $validator = self::resolveValidator($type);

    return $validator::generate($formatted);
  }

  /**
   * Valida um CPF.
   *
   * @param string $value
   * @return bool
   */
  public static function validateCpf(string $value): bool
  {
    return Cpf::validate($value);
  }

  /**
   * Formata um CPF.
   *
   * @param string $value
   * @return string
   */
  public static function formatCpf(string $value): string
  {
    return Cpf::format($value);
  }

  /**
   * Gera um CPF válido aleatório.
   *
   * @param bool $formatted
   * @return string
   */
  public static function generateCpf(bool $formatted = false): string
  {
    return Cpf::generate($formatted);
  }

  /**
   * Valida um CNPJ.
   *
   * @param string $value
   * @return bool
   */
  public static function validateCnpj(string $value): bool
  {
    return Cnpj::validate($value);
  }

  /**
   * Formata um CNPJ.
   *
   * @param string $value
   * @return string
   */
  public static function formatCnpj(string $value): string
  {
    return Cnpj::format($value);
  }

  /**
   * Gera um CNPJ válido aleatório.
   *
   * @param bool $formatted
   * @return string
   */
  public static function generateCnpj(bool $formatted = false): string
  {
    return Cnpj::generate($formatted);
  }

  /**
   * Valida um CEP.
   *
   * @param string $value
   * @return bool
   */
  public static function validateCep(string $value): bool
  {
    return Cep::validate($value);
  }

  /**
   * Formata um CEP.
   *
   * @param string $value
   * @return string
   */
  public static function formatCep(string $value): string
  {
    return Cep::format($value);
  }

  /**
   * Retorna a região de um CEP.
   *
   * @param string $value
   * @return string|null
   */
  public static function getCepRegion(string $value): ?string
  {
    return Cep::getRegion($value);
  }

  /**
   * Valida um telefone brasileiro.
   *
   * @param string $value
   * @return bool
   */
  public static function validateTelefone(string $value): bool
  {
    return Telefone::validate($value);
  }

  /**
   * Formata um telefone brasileiro.
   *
   * @param string $value
   * @return string
   */
  public static function formatTelefone(string $value): string
  {
    return Telefone::format($value);
  }

  /**
   * Verifica se é celular.
   *
   * @param string $value
   * @return bool
   */
  public static function isCellphone(string $value): bool
  {
    return Telefone::isCellphone($value);
  }

  /**
   * Verifica se é telefone fixo.
   *
   * @param string $value
   * @return bool
   */
  public static function isLandline(string $value): bool
  {
    return Telefone::isLandline($value);
  }

  /**
   * Valida uma CNH.
   *
   * @param string $value
   * @return bool
   */
  public static function validateCnh(string $value): bool
  {
    return Cnh::validate($value);
  }

  /**
   * Valida um PIS/PASEP/NIT/NIS.
   *
   * @param string $value
   * @return bool
   */
  public static function validatePis(string $value): bool
  {
    return PisPasep::validate($value);
  }

  /**
   * Valida um Título de Eleitor.
   *
   * @param string $value
   * @return bool
   */
  public static function validateTituloEleitor(string $value): bool
  {
    return TituloEleitor::validate($value);
  }

  /**
   * Valida um RENAVAM.
   *
   * @param string $value
   * @return bool
   */
  public static function validateRenavam(string $value): bool
  {
    return Renavam::validate($value);
  }

  /**
   * Valida uma Inscrição Estadual.
   *
   * @param string $value
   * @return bool
   */
  public static function validateInscricaoEstadual(string $value): bool
  {
    return InscricaoEstadual::validate($value);
  }

  /**
   * Valida Inscrição Estadual por UF.
   *
   * @param string $value
   * @param string $uf Sigla da UF
   * @return bool
   */
  public static function validateInscricaoEstadualByUf(string $value, string $uf): bool
  {
    return InscricaoEstadual::validateByUf($value, $uf);
  }

  /**
   * Valida uma Certidão (nascimento, casamento ou óbito).
   *
   * @param string $value
   * @return bool
   */
  public static function validateCertidao(string $value): bool
  {
    return Certidao::validate($value);
  }


  /**
   * Retorna a lista de tipos de documentos suportados.
   *
   * @return array<string>
   */
  public static function supportedTypes(): array
  {
    return array_unique(array_keys(self::VALIDATORS));
  }

  /**
   * Verifica se um tipo de documento é suportado.
   *
   * @param string $type
   * @return bool
   */
  public static function isSupported(string $type): bool
  {
    return isset(self::VALIDATORS[self::normalizeType($type)]);
  }

  /**
   * Valida múltiplos documentos de uma vez.
   *
   * @param array<string, string> $documents Array associativo [tipo => valor]
   * @return array<string, bool>  Array associativo [tipo => resultado]
   */
  public static function validateMany(array $documents): array
  {
    $results = [];

    foreach ($documents as $type => $value) {
      $results[$type] = self::validate($type, $value);
    }

    return $results;
  }

  /**
   * Resolve a classe validadora para o tipo de documento.
   *
   * @param string $type
   * @return string Nome da classe validadora
   *
   * @throws DocumentException Se o tipo não for suportado
   */
  private static function resolveValidator(string $type): string
  {
    $normalized = self::normalizeType($type);

    if (!isset(self::VALIDATORS[$normalized])) {
      throw DocumentException::unsupportedType($type);
    }

    return self::VALIDATORS[$normalized];
  }

  /**
   * Normaliza o tipo do documento para busca no mapa.
   *
   * @param string $type
   * @return string
   */
  private static function normalizeType(string $type): string
  {
    $type = strtolower(trim($type));
    $type = str_replace(["-", ".", " "], "_", $type);

    return $type;
  }
}
