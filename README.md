# Laravel Intelli DB

[![Latest Version on Packagist](https://img.shields.io/packagist/v/salehhashemi/laravel-intelli-db.svg?style=flat-square)](https://packagist.org/packages/salehhashemi/laravel-intelli-db)
[![Total Downloads](https://img.shields.io/packagist/dt/salehhashemi/laravel-intelli-db.svg?style=flat-square)](https://packagist.org/packages/salehhashemi/laravel-intelli-db)
[![GitHub Actions](https://img.shields.io/github/actions/workflow/status/salehhashemi1992/laravel-intelli-db/run-tests.yml?branch=master&label=tests)](https://github.com/salehhashemi1992/laravel-intelli-db/actions/workflows/run-tests.yml)

A Laravel package that provides an intelligent way to generate database-related components using 
OpenAI.
It extends the default `artisan make` commands in Laravel to automatically generate the content of each component using AI, based on the provided description.

## ToDo

The following features are planned to be added to the package:

- [x] `ai:rule` - Generate custom validation rules using AI
- [ ] `ai:migration` - Generate migration files using AI
- [ ] `ai:factory` - Generate factory files using AI
- [ ] `ai:model` - Generate model files using AI
- [ ] `ai:resource` - Generate resource files using AI
- [ ] `ai:seeder` - Generate seeder files using AI

Stay tuned for future updates as we continue to expand the capabilities of the Laravel Intelli DB package.

## Installation

1. Install the package via composer:
    ```
    composer require salehhashemi/laravel-intelli-db
    ```

2. Publish the configuration file:
    ```
    php artisan vendor:publish --provider="Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider"
    ```

3. Add your OpenAI API key to the `.env` file:
    ```
    OPEN_AI_KEY=your_openai_key
    ```

4. Optionally, you can change the default model used by OpenAI in the `.env` file:
    ```
   OPEN_AI_MODEL=gpt-4
    ```

## Usage

To create a new validation rule using AI, run the following command:

```
php artisan ai:rule YourRuleName
```

You can provide the description of the rule using the `--description` option:

```
php artisan ai:rule YourRuleName --description="Your rule description"
```

If you don't provide a description, it will ask for it interactively.

The generated rule class will be placed in the `app/Rules` directory.

## Example

To create a rule that validates a unique email address, run:

```
php artisan ai:rule UniqueEmail --description="Validate unique email address"
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Saleh Hashemi](https://github.com/salehhashemi1992)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.