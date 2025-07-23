# Sistema de Gestão de Ordens de Serviço

## Visão Geral

Este projeto é um sistema web para gestão de ordens de serviço, clientes, equipamentos, usuários e relatórios. Desenvolvido em PHP com front-end em HTML, CSS e JavaScript, o sistema é modular, seguro e de fácil manutenção.

---

## Estrutura do Projeto

```
sistemav2/
├── api/                # Endpoints PHP para comunicação AJAX/REST
├── assets/             # Arquivos estáticos (CSS, JS, imagens)
├── includes/           # Funções e lógicas reutilizáveis
├── config/             # Configurações do sistema (ex: banco de dados)
├── views/              # Componentes visuais reutilizáveis
├── Documentação/       # Documentação do projeto
├── *.php               # Páginas principais do sistema
├── *.sql               # Scripts SQL
├── debug*, test*       # Arquivos de depuração e teste
```

---

## Instalação

1. **Pré-requisitos:**
   - PHP 7.4+
   - MySQL/MariaDB
   - Servidor web (Apache recomendado)

2. **Configuração do Banco de Dados:**
   - Edite `config/database.php` com as credenciais do seu banco.
   - Importe os scripts SQL necessários (ex: `modelos-insert.sql`).

3. **Configuração do Servidor:**
   - Coloque a pasta do projeto no diretório do seu servidor web (ex: `htdocs` do XAMPP).
   - Acesse `http://localhost/sistemav2/login.php` para iniciar.

---

## Principais Módulos

- **Clientes:** Cadastro e gerenciamento de clientes.
- **Equipamentos:** Controle de equipamentos, marcas, modelos e tipos.
- **Ordens de Serviço:** Criação e acompanhamento de ordens.
- **Relatórios:** Geração de relatórios diversos.
- **Checklist:** Controle de checklists para ordens/equipamentos.
- **Usuários:** Gerenciamento de usuários e permissões.
- **Dashboard:** Visão geral do sistema.
- **Empresa:** Dados e configurações da empresa.

---

## APIs e Endpoints

Os arquivos em `api/` expõem endpoints para consumo via AJAX/Fetch. Exemplos:

- `api/clientes.php`  
  - `GET`: lista clientes  
  - `POST`: cadastra novo cliente  
  - `PUT`: edita cliente  
  - `DELETE`: remove cliente

- `api/ordens.php`  
  - `GET`: lista ordens  
  - `POST`: cria ordem  
  - `PUT`: atualiza ordem  
  - `DELETE`: exclui ordem

- `api/auth.php`  
  - `POST login`: autentica usuário  
  - `POST logout`: encerra sessão  
  - `GET session`: verifica sessão

> **Obs:** Consulte os arquivos em `api/` para detalhes de parâmetros e respostas.

---

## Segurança

- Autenticação via sessão PHP.
- Senhas devem ser armazenadas com hash seguro.
- APIs validam sessão antes de executar ações sensíveis.
- Remova arquivos de teste e depuração do ambiente de produção.

---

## Recomendações

- Documente endpoints e fluxos de uso.
- Utilize HTTPS em produção.
- Mantenha scripts de migração/versionamento do banco de dados.
- Modularize scripts JS e CSS para facilitar manutenção.

---

## Licença

Este projeto é privado e não possui licença aberta. Para uso, modificação ou distribuição, consulte o responsável pelo sistema. 