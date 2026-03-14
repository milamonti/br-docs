# BrDocs - Validação de Documentos Brasileiros

Biblioteca PHP para validação, formatação e geração de documentos brasileiros.

## Instalação

```bash
composer require br-validation/docs
```

## Documentos Suportados

| Documento          | Tipo             | Validação | Formatação | Geração |
| ------------------ | ---------------- | --------- | ---------- | ------- |
| CPF                | `cpf`            | ✅        | ✅         | ✅      |
| CNPJ               | `cnpj`           | ✅        | ✅         | ✅      |
| CPF/CNPJ (auto)    | `cpf_cnpj`       | ✅        | ✅         | —       |
| CEP                | `cep`            | ✅        | ✅         | ✅      |
| CNH                | `cnh`            | ✅        | ✅         | ✅      |
| PIS/PASEP/NIT      | `pis`            | ✅        | ✅         | ✅      |
| Título de Eleitor  | `titulo_eleitor` | ✅        | ✅         | ✅      |
| Inscrição Estadual | `ie`             | ✅        | ✅         | ✅      |
| RENAVAM            | `renavam`        | ✅        | ✅         | ✅      |
| Certidão           | `certidao`       | ✅        | ✅         | ✅      |
| Telefone           | `telefone`       | ✅        | ✅         | ✅      |

## Uso Rápido

### Usando a classe de fachada `BrDocs`

```php
use Validation\BrDocs\BrDocs;

// Validação genérica por tipo
BrDocs::validate('cpf', '123.456.789-09');    // true ou false
BrDocs::validate('cnpj', '11.222.333/0001-81'); // true ou false
BrDocs::validate('cep', '01001-000');          // true ou false

// Validar e lançar exceção se inválido
BrDocs::validateOrFail('cpf', '000.000.000-00'); // Lança DocumentException

// Formatação
BrDocs::format('cpf', '12345678909');        // '123.456.789-09'
BrDocs::format('cnpj', '11222333000181');     // '11.222.333/0001-81'
BrDocs::format('cep', '01001000');            // '01001-000'
BrDocs::format('telefone', '11999887766');    // '(11) 99988-7766'

// Sanitização (remover formatação)
BrDocs::sanitize('cpf', '123.456.789-09');   // '12345678909'

// Geração de documentos válidos
BrDocs::generate('cpf');                      // '12345678909'
BrDocs::generate('cpf', true);               // '123.456.789-09'
BrDocs::generate('cnpj', true);              // '11.222.333/0001-81'

// Validação múltipla
BrDocs::validateMany([
    'cpf'  => '123.456.789-09',
    'cnpj' => '11.222.333/0001-81',
    'cep'  => '01001-000',
]); // ['cpf' => true, 'cnpj' => true, 'cep' => true]
```

### Usando métodos de atalho

```php
use Validation\BrDocs\BrDocs;

// CPF
BrDocs::validateCpf('123.456.789-09');
BrDocs::formatCpf('12345678909');
BrDocs::generateCpf(true);

// CNPJ
BrDocs::validateCnpj('11.222.333/0001-81');
BrDocs::formatCnpj('11222333000181');
BrDocs::generateCnpj(true);

// CPF/CNPJ automático
BrDocs::validateCpfCnpj('123.456.789-09');  // Detecta CPF e valida
BrDocs::validateCpfCnpj('11.222.333/0001-81'); // Detecta CNPJ e valida
BrDocs::detectCpfCnpj('12345678909');        // 'cpf'
BrDocs::detectCpfCnpj('11222333000181');     // 'cnpj'

// CEP
BrDocs::validateCep('01001-000');
BrDocs::formatCep('01001000');
BrDocs::getCepRegion('01001000'); // 'Grande São Paulo'

// Telefone
BrDocs::validateTelefone('(11) 99988-7766');
BrDocs::formatTelefone('11999887766');
BrDocs::isCellphone('11999887766');  // true
BrDocs::isLandline('1133334444');    // true

// Outros
BrDocs::validateCnh('04965101679');
BrDocs::validatePis('123.45678.90-1');
BrDocs::validateTituloEleitor('123456780612');
BrDocs::validateRenavam('63abortar905');
BrDocs::validateInscricaoEstadual('123456789');
BrDocs::validateInscricaoEstadualByUf('110042490114', 'SP');
BrDocs::validateCertidao('10452601552014100032003000000012');
```

### Usando validadores individuais

```php
use Validation\BrDocs\Validators\Cpf;
use Validation\BrDocs\Validators\Cnpj;
use Validation\BrDocs\Validators\Cep;

Cpf::validate('123.456.789-09');
Cpf::format('12345678909');
Cpf::sanitize('123.456.789-09');
Cpf::generate(true);

Cnpj::validate('11.222.333/0001-81');
Cep::getRegion('01001-000');
```

## Utilitários

```php
// Listar tipos suportados
BrDocs::supportedTypes();
// ['cpf', 'cnpj', 'cpf_cnpj', 'cpfcnpj', 'cep', 'cnh', 'pis', ...]

// Verificar se um tipo é suportado
BrDocs::isSupported('cpf');  // true
BrDocs::isSupported('xyz');  // false
```

## Tratamento de Erros

```php
use Validation\BrDocs\BrDocs;
use Validation\BrDocs\Exceptions\DocumentException;

try {
    BrDocs::validateOrFail('cpf', '000.000.000-00');
} catch (DocumentException $e) {
    echo $e->getMessage(); // 'O CPF informado "000.000.000-00" é inválido.'
}

try {
    BrDocs::validate('xyz', '12345');
} catch (DocumentException $e) {
    echo $e->getMessage(); // 'O tipo de documento "xyz" não é suportado.'
}
```

## Licença

MIT
