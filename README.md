# Painel de Controle - Laravel com Tall Stack

## Instalação

1. Clone o repositório
2. Instale as dependências
3. Copie o arquivo .env.example para .env
4. Gere uma nova chave para a aplicação
5. Execute as migrations
6. Execute os seeders
7. Inicie o servidor

```bash
git clone git@github.com:wagnerferreirasi/painel.git
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Tecnologias

- Laravel 10
- Livewire 2
- AlpineJS 3
- TailwindCSS 2

## Licença

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
