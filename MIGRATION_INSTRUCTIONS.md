# Instruções para Atualizar o Banco de Dados

## Executar Migration

Para adicionar o campo de imagem aos filmes, execute:

```bash
cd backend
php artisan migrate
```

## Criar Link Simbólico para Storage (se ainda não existir)

```bash
php artisan storage:link
```

Isso criará um link simbólico de `public/storage` para `storage/app/public`, permitindo que as imagens sejam acessíveis publicamente.

## Testar Upload

Após executar os comandos acima, você poderá:
- Fazer upload de imagens ao criar/editar filmes
- Visualizar as imagens na listagem e nos detalhes
- As imagens serão armazenadas em `storage/app/public/filmes/`
