# 🗳️ Sistema de Votação de Fotos

Sistema web para votação em fotos desenvolvido com Laravel 11 e Vue.js.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen.svg)](#testes)
[![Status](https://img.shields.io/badge/Status-Estável-success.svg)](#status)

## 📋 Sobre o Projeto

Sistema de votação que permite aos usuários visualizar e votar em fotos. Desenvolvido para ser simples, seguro e eficiente.

### ✨ Funcionalidades

- 🔐 **Autenticação com SARAM** - Login seguro com número de 7 dígitos
- 🗳️ **Sistema de Votação** - Vote e remova votos em tempo real
- 🖼️ **Galeria de Fotos** - Interface responsiva para visualização
- 👥 **Controle de Acesso** - Roles de Admin e Voter
- 🧪 **Testes Automatizados** - Cobertura completa de autenticação

## 🚀 Início Rápido

### Pré-requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- NPM

### Instalação

1. **Clone o repositório**
```bash
git clone <url-do-repositorio>
cd vote
```

2. **Instale as dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```bash
php artisan migrate
php artisan db:seed
```

5. **Compile os assets**
```bash
npm run build
```

6. **Inicie o servidor**
```bash
php artisan serve
```

Acesse: `http://localhost:8000`

## 👥 Usuários de Teste

### Admin
- **SARAM:** 1234567
- **Senha:** password
- **Permissões:** Acesso completo

### Voter
- **SARAM:** 9876543
- **Senha:** password
- **Permissões:** Votação e visualização

## 🧪 Testes

Execute os testes automatizados:

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter=RegistrationTest
```

### Cobertura de Testes
- ✅ AuthenticationTest
- ✅ RegistrationTest
- ✅ EmailVerificationTest
- ✅ PasswordConfirmationTest
- ✅ PasswordResetTest
- ✅ PasswordUpdateTest

## 🏗️ Arquitetura

### Backend (Laravel 11)
- **Controllers:** Lógica de apresentação
- **Services:** Regras de negócio
- **Models:** Eloquent ORM
- **Middleware:** Autenticação e autorização

### Frontend (Vue.js + Inertia.js)
- **Components:** Componentes reutilizáveis
- **Pages:** Páginas da aplicação
- **Layouts:** Templates base

### Banco de Dados
- **SQLite:** Desenvolvimento e testes
- **Migrations:** Controle de versão do schema
- **Seeders:** Dados iniciais

## 📁 Estrutura do Projeto

```
vote/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/              # Models Eloquent
│   ├── Services/            # Lógica de negócio
│   └── ...
├── resources/
│   ├── js/                  # Vue.js components
│   └── views/               # Blade templates
├── tests/
│   ├── Feature/             # Testes de integração
│   └── Unit/                # Testes unitários
├── docs/                    # Documentação
│   ├── BOAS_PRATICAS.md
│   ├── CHANGELOG.md
│   └── PROXIMOS_PASSOS.md
└── STATUS_PROJETO.md        # Status atual
```

## 📚 Documentação

- [📋 Status do Projeto](STATUS_PROJETO.md)
- [✅ Boas Práticas](docs/BOAS_PRATICAS.md)
- [🔄 Changelog](docs/CHANGELOG.md)
- [🎯 Próximos Passos](docs/PROXIMOS_PASSOS.md)

## 🛠️ Desenvolvimento

### Comandos Úteis

```bash
# Servidor de desenvolvimento
php artisan serve

# Compilar assets (desenvolvimento)
npm run dev

# Executar testes
php artisan test

# Limpar cache
php artisan optimize:clear

# Verificar rotas
php artisan route:list
```

### Padrões de Código

- **PSR-12:** Padrão de codificação PHP
- **Vue.js Style Guide:** Convenções do Vue.js
- **Laravel Conventions:** Padrões do framework

## 🔧 Tecnologias

### Backend
- **Laravel 11** - Framework PHP
- **SQLite** - Banco de dados
- **PHPUnit** - Testes automatizados

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Inertia.js** - SPA sem API
- **Tailwind CSS** - Framework CSS
- **Vite** - Build tool

## 📈 Status Atual

- ✅ **Sistema de Autenticação** - Funcionando
- ✅ **Votação em Fotos** - Implementado
- ✅ **Testes Automatizados** - 6/6 passando
- ✅ **Interface Responsiva** - Completa
- 🔄 **Performance** - Em otimização

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

**Desenvolvido com ❤️ usando Laravel e Vue.js**
