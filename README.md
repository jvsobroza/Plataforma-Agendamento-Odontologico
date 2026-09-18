# Plataforma de Agendamento Odontológico

Sistema web para gestão de clínicas odontológicas, desenvolvido como **Trabalho de Conclusão de Curso (TCC)** do curso de **Tecnologia em Análise e Desenvolvimento de Sistemas**.

A aplicação permite gerenciar filiais, pacientes, serviços, planos de tratamento e agendamentos, com controle de acesso diferenciado para **dentistas (administradores)** e **secretárias**.

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat&logo=bootstrap&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-em%20desenvolvimento-2496ED?style=flat&logo=docker&logoColor=white)
![Evolution API](https://img.shields.io/badge/Evolution%20API-em%20desenvolvimento-25D366?style=flat&logo=whatsapp&logoColor=white)

---

## Sobre o projeto

A **Plataforma de Agendamento Odontológico** foi criada para digitalizar e organizar a rotina de uma clínica de odontologia, substituindo controles manuais por um sistema centralizado. O sistema oferece uma interface web (Blade + Bootstrap) para que dentistas e secretárias possam gerenciar toda a operação da clínica.

## Funcionalidades

- **Autenticação e controle de acesso**
  - Login com dois perfis de usuário: `Dentista` (administrador) e `Secretária`.
  - Middlewares dedicados (`CheckDentista`, `CheckSecretaria`) que restringem rotas e ações conforme o perfil.
- **Gestão de filiais**
  - Cadastro de filiais/unidades da clínica, com endereço, agenda e serviços oferecidos.
- **Gestão de pacientes**
  - Cadastro de pacientes com CPF, telefone, data de nascimento e observações médicas.
- **Agendamentos**
  - Criação e controle de consultas, com status de agendamento e status de pagamento.
- **Planos de tratamento**
  - Definição de planos por paciente, com serviços planejados e concluídos.
- **Serviços e serviços de tratamento**
  - Catálogo de serviços da clínica, associados a filiais e a planos de tratamento (com tempo estimado e preço).
- **Gestão de secretárias**
  - Cadastro, edição e restauração (soft delete/restore) de contas de secretárias pelo dentista/administrador.

## Tecnologias utilizadas

**Back-end**
- [PHP 8.3](https://www.php.net/)
- [Laravel 13](https://laravel.com/)
- SQLite (padrão em desenvolvimento, configurável para MySQL/PostgreSQL)

**Front-end**
- Blade (templates do Laravel)
- [Bootstrap 5](https://getbootstrap.com/)

**Em desenvolvimento**
- [Docker](https://www.docker.com/) — containerização da aplicação para facilitar setup e deploy e para uso da Evolution API
- [Evolution API](https://evolution-api.com/) — integração via WhatsApp para notificações e agendamentos (webhook de pacientes)

## Estrutura do banco de dados

Principais entidades do sistema:

| Tabela | Descrição |
|---|---|
| `usuarios` | Dentistas e secretárias (campo `tipo`: 1 = dentista, 2 = secretária) |
| `filials` | Filiais/unidades da clínica |
| `pacientes` | Cadastro de pacientes |
| `servicos` | Catálogo de serviços oferecidos |
| `filial_servico` | Relação N:N entre filiais e serviços |
| `agendamentos` | Consultas agendadas por paciente/filial |
| `plano_tratamentos` | Planos de tratamento por paciente |
| `servico_tratamentos` | Serviços vinculados a um plano de tratamento e a um agendamento |

## Como executar o projeto

### Pré-requisitos

- PHP >= 8.3
- Composer
- Node.js e npm
- [Laragon](https://laragon.org/download/)
- Git

### Passo a passo
1. **Instale o Laragon** e abra o programa.
2. **Clone o repositório dentro da pasta `www` do Laragon:**
```bash
   cd C:\laragon\www
   git clone https://github.com/jvsobroza/Plataforma-Agendamento-Odontologico.git
   cd Plataforma-Agendamento-Odontologico
```
 
3. **Inicie os serviços do Laragon** clicando em **Start All** (sobe o Apache/Nginx e o MySQL).
4. **Instale as dependências PHP** (use o terminal do próprio Laragon, já com o PHP e o Composer configurados no PATH):
```bash
   composer install
```
 
5. **Copie o arquivo de variáveis de ambiente:**
```bash
   cp .env.example .env
```
 
6. **Gere a chave da aplicação:**
```bash
   php artisan key:generate
```
 
7. **Configure o arquivo `.env`.** Por padrão o projeto usa SQLite, mas para usar o MySQL do Laragon, ajuste:
```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=plataforma_agendamento
   DB_USERNAME=root
   DB_PASSWORD=
```
   Crie o banco `plataforma_agendamento` pelo HeidiSQL (já incluso no Laragon) ou pelo terminal.
 
   > Se preferir usar SQLite em vez do MySQL, mantenha `DB_CONNECTION=sqlite` e crie o arquivo com `touch database/database.sqlite`.
 
8. **Execute as migrations:**
```bash
   php artisan migrate
```
 
9. **Instale as dependências JavaScript e compile os assets:**
```bash
   npm install
   npm run build
```

## Perfis de acesso

| Perfil | Permissões |
|---|---|
| **Dentista** (`tipo = 1`) | Acesso total: gerencia filiais, serviços, planos de tratamento, secretárias e agendamentos |
| **Secretária** (`tipo = 2`) | Acesso restrito: gerencia pacientes e agendamentos |

## Status do projeto

Projeto desenvolvido para fins acadêmicos (TCC), em desenvolvimento contínuo. Próximas entregas:

- **Docker** — containerização da aplicação (app, banco de dados e assets) para facilitar setup e deploy.
- **Evolution API** — integração com WhatsApp para envio de notificações e confirmação de agendamentos.

## Autor

Desenvolvido por [**jvsobroza**](https://github.com/jvsobroza).

## Licença

Este projeto está sob a licença MIT.
