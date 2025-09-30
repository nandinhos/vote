# Otimização Automática de Imagens

## Visão Geral

O sistema agora possui otimização automática de imagens durante o upload. Todas as fotos enviadas são automaticamente redimensionadas e comprimidas para garantir melhor performance e economia de espaço.

## Funcionalidades

### ✅ Redimensionamento Automático
- **Largura máxima**: 1920px (configurável)
- **Altura máxima**: 1080px (configurável)
- **Mantém proporção**: Sim (configurável)

### ✅ Compressão Inteligente
- **Qualidade JPEG**: 85% (configurável)
- **Formatos suportados**: JPEG, PNG, WebP
- **Tamanho máximo**: 2MB (configurável)

### ✅ Otimização Condicional
- Só otimiza se necessário (configurável)
- Verifica dimensões e tamanho do arquivo
- Preserva qualidade visual

## Configuração

### Arquivo de Configuração
As configurações estão em `config/image_optimization.php`:

```php
return [
    'max_width' => env('IMAGE_MAX_WIDTH', 1920),
    'max_height' => env('IMAGE_MAX_HEIGHT', 1080),
    'quality' => env('IMAGE_QUALITY', 85),
    'max_file_size' => env('IMAGE_MAX_FILE_SIZE', 2048),
    'output_format' => env('IMAGE_OUTPUT_FORMAT', 'jpeg'),
    'maintain_aspect_ratio' => env('IMAGE_MAINTAIN_ASPECT_RATIO', true),
    'optimize_only_if_needed' => env('IMAGE_OPTIMIZE_ONLY_IF_NEEDED', true),
];
```

### Variáveis de Ambiente
Adicione ao seu arquivo `.env`:

```env
# Configurações de Otimização de Imagem
IMAGE_MAX_WIDTH=1920
IMAGE_MAX_HEIGHT=1080
IMAGE_QUALITY=85
IMAGE_MAX_FILE_SIZE=2048
IMAGE_OUTPUT_FORMAT=jpeg
IMAGE_MAINTAIN_ASPECT_RATIO=true
IMAGE_OPTIMIZE_ONLY_IF_NEEDED=true
```

## Parâmetros Explicados

| Parâmetro | Descrição | Valores | Padrão |
|-----------|-----------|---------|---------|
| `IMAGE_MAX_WIDTH` | Largura máxima em pixels | Número inteiro | 1920 |
| `IMAGE_MAX_HEIGHT` | Altura máxima em pixels | Número inteiro | 1080 |
| `IMAGE_QUALITY` | Qualidade da compressão | 1-100 | 85 |
| `IMAGE_MAX_FILE_SIZE` | Tamanho máximo em KB | Número inteiro | 2048 |
| `IMAGE_OUTPUT_FORMAT` | Formato de saída | jpeg, png, webp | jpeg |
| `IMAGE_MAINTAIN_ASPECT_RATIO` | Manter proporção | true, false | true |
| `IMAGE_OPTIMIZE_ONLY_IF_NEEDED` | Otimizar só se necessário | true, false | true |

## Como Funciona

### 1. Upload de Foto
Quando você faz upload de uma foto através do sistema:

1. **Verificação**: O sistema verifica se a imagem precisa ser otimizada
2. **Redimensionamento**: Se necessário, redimensiona mantendo a proporção
3. **Compressão**: Aplica compressão com a qualidade configurada
4. **Salvamento**: Salva a imagem otimizada no formato especificado

### 2. Critérios de Otimização
A imagem será otimizada se:
- Largura > `IMAGE_MAX_WIDTH`
- Altura > `IMAGE_MAX_HEIGHT`
- Tamanho > `IMAGE_MAX_FILE_SIZE`
- `IMAGE_OPTIMIZE_ONLY_IF_NEEDED` = false (força otimização)

## Exemplos de Uso

### Configuração para Alta Qualidade
```env
IMAGE_MAX_WIDTH=2560
IMAGE_MAX_HEIGHT=1440
IMAGE_QUALITY=95
IMAGE_MAX_FILE_SIZE=5120
IMAGE_OUTPUT_FORMAT=png
```

### Configuração para Web Otimizada
```env
IMAGE_MAX_WIDTH=1280
IMAGE_MAX_HEIGHT=720
IMAGE_QUALITY=75
IMAGE_MAX_FILE_SIZE=1024
IMAGE_OUTPUT_FORMAT=webp
```

### Configuração para Mobile
```env
IMAGE_MAX_WIDTH=800
IMAGE_MAX_HEIGHT=600
IMAGE_QUALITY=70
IMAGE_MAX_FILE_SIZE=512
IMAGE_OUTPUT_FORMAT=jpeg
```

## Benefícios

### ✅ Performance
- **Carregamento mais rápido** das páginas
- **Menor uso de banda** para usuários
- **Melhor experiência** em dispositivos móveis

### ✅ Armazenamento
- **Economia de espaço** no servidor
- **Redução de custos** de storage
- **Backup mais eficiente**

### ✅ Automático
- **Sem intervenção manual** necessária
- **Processo transparente** para o usuário
- **Configuração flexível** via ambiente

## Troubleshooting

### Erro: "Class not found"
```bash
composer dump-autoload
php artisan config:cache
```

### Erro: "GD extension not found"
```bash
# Ubuntu/Debian
sudo apt-get install php-gd

# CentOS/RHEL
sudo yum install php-gd
```

### Verificar Configurações
```bash
php artisan tinker
>>> config('image_optimization')
```

## Monitoramento

### Verificar Otimizações
As mensagens de sucesso foram atualizadas para indicar quando uma imagem foi otimizada:
- "Foto adicionada e otimizada com sucesso!"
- "Foto atualizada e otimizada com sucesso!"

### Logs
Para debug, você pode adicionar logs no service `ImageOptimizationService.php`.

---

**Nota**: Esta funcionalidade é aplicada automaticamente a todos os uploads de fotos. Não é necessário converter imagens existentes, apenas os novos uploads serão otimizados.