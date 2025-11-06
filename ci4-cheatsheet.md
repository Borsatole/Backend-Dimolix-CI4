# 🔥 CodeIgniter 4 – Guia Completo de Comandos Spark

> **Guia de referência rápida para desenvolvimento com CodeIgniter 4**

---

## 📋 Índice
- [Migrations](#-migrations)
- [Seeders](#-seeders)
- [Models](#-models)
- [Controllers](#-controllers)
- [Entities](#-entities)
- [Filters](#-filters)
- [Validation](#-validation)
- [Commands](#-commands)
- [Rotas](#-rotas)
- [Cache](#-cache)
- [Servidor](#-servidor)
- [Fluxos Completos](#-fluxos-completos)

---

## 🗄️ Migrations

### Comandos Básicos
```bash
# Criar nova migration
php spark make:migration NomeDaMigration

# Criar migration para tabela específica
php spark make:migration CreateUsersTable
php spark make:migration AddEmailToUsers
```

### Executar Migrations
```bash
# Rodar todas migrations pendentes
php spark migrate

# Rodar migration específica
php spark migrate --path=app/Database/Migrations/2024-01-01-120000_CreateUsersTable.php

# Rodar até versão específica
php spark migrate --version=20240101120000

# Rodar em namespace específico
php spark migrate --namespace=MyApp
```

### Reverter Migrations
```bash
# Reverter último batch
php spark migrate:rollback

# Reverter todos os batches
php spark migrate:reset

# Reverter e recriar tudo
php spark migrate:refresh

# Reverter e recriar com seeders
php spark migrate:refresh --seed
```

### Status e Informações
```bash
# Ver status de todas migrations
php spark migrate:status

# Ver informações detalhadas
php spark migrate:status --group=default
```

💡 **Dica:** Para resetar banco completamente:
```bash
php spark migrate:reset && php spark migrate && php spark db:seed DatabaseSeeder
```

---

## 🌱 Seeders

### Criar Seeders
```bash
# Criar novo seeder
php spark make:seeder NomeDoSeeder

# Exemplos práticos
php spark make:seeder UsersSeeder
php spark make:seeder ProductsSeeder
```

### Executar Seeders
```bash
# Executar seeder específico
php spark db:seed NomeDoSeeder

# Executar DatabaseSeeder (que chama outros)
php spark db:seed DatabaseSeeder

# Executar em ambiente específico
php spark db:seed NomeDoSeeder --environment=testing
```

💡 **Exemplo de DatabaseSeeder.php:**
```php
public function run()
{
    $this->call('UsersSeeder');
    $this->call('ProductsSeeder');
    $this->call('CategoriesSeeder');
}
```

---

## 🧠 Models

### Criar Models
```bash
# Model simples
php spark make:model NomeDoModel

# Model com todas as opções
php spark make:model User --table users --return entity

# Exemplos práticos
php spark make:model ClienteModel
php spark make:model Product --table products
```

### Estrutura de Model
```php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'password'];
    
    // Timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Validação
    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email]'
    ];
    
    // Soft Deletes
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';
}
```

---

## 🎮 Controllers

### Criar Controllers
```bash
# Controller simples
php spark make:controller NomeDoController

# Controller RESTful
php spark make:controller Api/Users --restful

# Controller com Resource (CRUD completo)
php spark make:controller Admin/Products --resource

# Controller com sufixo
php spark make:controller Home --suffix
```

### Estrutura de Controller
```php
namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }
}
```

---

## 🏗️ Entities

### Criar Entities
```bash
# Entity simples
php spark make:entity User

# Entity com namespace
php spark make:entity Entities/Product
```

### Estrutura de Entity
```php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $attributes = [
        'id'       => null,
        'name'     => null,
        'email'    => null,
        'created_at' => null,
    ];
    
    protected $casts = [
        'id'         => 'integer',
        'created_at' => 'datetime',
    ];
    
    // Mutators
    public function setPassword(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }
}
```

---

## 🛡️ Filters

### Criar Filters
```bash
# Filter simples
php spark make:filter Auth

# Filter com namespace
php spark make:filter Security/RateLimit
```

### Estrutura de Filter
```php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // After action
    }
}
```

---

## ✅ Validation

### Criar Validation Rules
```bash
# Criar validation personalizada
php spark make:validation UserRules
```

### Estrutura de Validation
```php
namespace App\Validation;

class UserRules
{
    public function unique_username(string $username, string $fields, array $data): bool
    {
        $model = new \App\Models\UserModel();
        return $model->where('username', $username)->first() === null;
    }
}
```

---

## ⚡ Commands

### Criar Commands Personalizados
```bash
# Command simples
php spark make:command ProcessOrders

# Estrutura em namespace
php spark make:command Tasks/SendEmails
```

### Estrutura de Command
```php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;

class ProcessOrders extends BaseCommand
{
    protected $group       = 'app';
    protected $name        = 'orders:process';
    protected $description = 'Process pending orders';
    
    public function run(array $params)
    {
        $this->write('Processing orders...', 'green');
        // Lógica aqui
    }
}
```

### Executar Command
```bash
php spark orders:process
```

---

## 🛣️ Rotas

### Visualizar Rotas
```bash
# Ver todas as rotas registradas
php spark routes

# Filtrar rotas
php spark routes --host example.com

# Ver rotas CLI
php spark routes --cli-only
```

---

## 🧹 Cache

### Gerenciar Cache
```bash
# Limpar todo cache
php spark cache:clear

# Limpar cache específico
php spark cache:clear --driver file

# Ver informações do cache
php spark cache:info
```

---

## 🚀 Servidor

### Iniciar Servidor
```bash
# Servidor padrão (localhost:8080)
php spark serve

# Servidor em porta específica
php spark serve --port=3000

# Servidor em host específico
php spark serve --host=0.0.0.0 --port=8080
```

---

## 🔧 Outros Comandos Úteis

### Informações do Sistema
```bash
# Ver versão do CodeIgniter
php spark --version

# Ver todas as opções do Spark
php spark list

# Ver informações do PHP
php spark env
```

### Namespace e Descoberta
```bash
# Ver namespaces descobertos
php spark namespaces
```

### Key Generation
```bash
# Gerar chave de encriptação
php spark key:generate
```

---

## 🔄 Fluxos Completos

### 🆕 Criar Nova Feature (CRUD Completo)
```bash
# 1. Criar migration
php spark make:migration CreateProductsTable

# 2. Criar model
php spark make:model Product

# 3. Criar entity (opcional)
php spark make:entity Product

# 4. Criar controller
php spark make:controller Admin/Products --resource

# 5. Criar seeder
php spark make:seeder ProductsSeeder

# 6. Executar
php spark migrate
php spark db:seed ProductsSeeder
```

### 🗑️ Resetar Banco Completamente
```bash
# Opção 1: Reset completo
php spark migrate:reset
php spark migrate
php spark db:seed DatabaseSeeder

# Opção 2: Refresh com seeders
php spark migrate:refresh --seed
```

### 🔄 Atualizar Tabela Existente
```bash
# 1. Criar migration de alteração
php spark make:migration AddStatusToProducts

# 2. Executar migration
php spark migrate

# 3. Reverter se necessário
php spark migrate:rollback
```

### 🧪 Setup Ambiente de Testes
```bash
# 1. Criar migrations
php spark migrate --environment=testing

# 2. Popular dados de teste
php spark db:seed TestDataSeeder --environment=testing
```

---

## 📚 Referências Rápidas

### Tipos de Dados para Migrations
```php
$forge->addField([
    'id'          => ['type' => 'INT', 'auto_increment' => true],
    'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
    'email'       => ['type' => 'VARCHAR', 'constraint' => 255],
    'price'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
    'description' => ['type' => 'TEXT'],
    'is_active'   => ['type' => 'BOOLEAN', 'default' => true],
    'created_at'  => ['type' => 'DATETIME', 'null' => true],
]);
```

### Validation Rules Comuns
```php
'required|min_length[3]|max_length[50]'
'required|valid_email|is_unique[users.email]'
'required|numeric|greater_than[0]'
'required|in_list[active,inactive,pending]'
'permit_empty|valid_url'
```

### Model Query Examples
```php
// Find
$user = $model->find($id);
$users = $model->findAll();

// Where
$users = $model->where('status', 'active')->findAll();
$user = $model->where('email', $email)->first();

// Insert/Update/Delete
$model->insert($data);
$model->update($id, $data);
$model->delete($id);

// Pagination
$users = $model->paginate(10);
$pager = $model->pager;
```

---

## 🎯 Atalhos Pro

```bash
# Setup completo de projeto novo
alias ci-setup='php spark migrate && php spark db:seed DatabaseSeeder'

# Reset rápido
alias ci-reset='php spark migrate:reset && php spark migrate && php spark db:seed DatabaseSeeder'

# Servidor dev
alias ci-serve='php spark serve --host=0.0.0.0 --port=8080'

# Limpar tudo
alias ci-clear='php spark cache:clear && php spark routes'
```

---

## 💡 Boas Práticas

✅ **FAZER:**
- Use migrations para todas as mudanças no banco
- Crie seeders para dados iniciais e de teste
- Use entities para lógica de negócio
- Implemente validação nos models
- Use filters para autenticação/autorização
- Versione suas migrations com timestamps

❌ **EVITAR:**
- Editar migrations já executadas em produção
- Fazer queries SQL diretas sem necessidade
- Ignorar validação de dados
- Commitar arquivos de environment (.env)
- Usar controllers para lógica de negócio complexa

---

**📝 Última atualização:** 2025  
**🔗 Documentação oficial:** [codeigniter.com/user_guide](https://codeigniter.com/user_guide/)