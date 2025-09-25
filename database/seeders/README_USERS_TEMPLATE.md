# Template para Dados do Efetivo Votante

Este diretório contém arquivos modelo para facilitar a criação de um seeder com os dados do efetivo votante.

## Arquivos Disponíveis

### 1. `users_template.csv`
Formato CSV para facilitar a edição em planilhas (Excel, Google Sheets, etc.)

### 2. `users_template.json`
Formato JSON para quem prefere trabalhar com dados estruturados

## Estrutura dos Dados

Cada usuário deve conter os seguintes campos:

| Campo | Tipo | Descrição | Obrigatório |
|-------|------|-----------|-------------|
| `name` | string | Nome completo do militar | ✅ |
| `saram` | string | Número de identificação militar (usado como login) | ✅ |
| `password` | string | Senha inicial (será criptografada automaticamente) | ✅ |
| `role` | string | Função: 'admin' ou 'voter' | ✅ |

## Instruções de Uso

### Para CSV:
1. Abra o arquivo `users_template.csv` em uma planilha
2. Substitua os dados de exemplo pelos dados reais do efetivo
3. Mantenha o cabeçalho (primeira linha)
4. Salve o arquivo como CSV

### Para JSON:
1. Abra o arquivo `users_template.json` em um editor de texto
2. Substitua os dados de exemplo pelos dados reais do efetivo
3. Mantenha a estrutura JSON válida
4. Salve o arquivo

## Regras Importantes

- **SARAM deve ser único**: Cada militar deve ter um número SARAM diferente
- **Senhas**: Recomenda-se usar senhas temporárias que os usuários devem alterar no primeiro acesso
- **Roles disponíveis**:
  - `voter`: Usuário comum que pode votar
  - `admin`: Administrador com acesso ao painel administrativo

## Exemplo de Dados

```csv
name,saram,password,role
"João Silva Santos","123456","senha123","voter"
"Maria Oliveira Costa","234567","senha456","voter"
"Carlos Eduardo Lima","345678","senha789","admin"
```

## Próximos Passos

Após preencher os dados:
1. Solicite a criação do seeder
2. O seeder será criado automaticamente baseado nos dados fornecidos
3. Execute o seeder para popular o banco de dados

## Dicas de Segurança

- Use senhas temporárias simples para o primeiro acesso
- Configure uma política para que os usuários alterem a senha no primeiro login
- Mantenha o arquivo com dados sensíveis fora do controle de versão