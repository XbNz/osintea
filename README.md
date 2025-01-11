![Release](https://img.shields.io/github/v/release/XbNz/osintea?style=for-the-badge)
![License](https://img.shields.io/github/license/XbNz/osintea?style=for-the-badge)

![Test suite](https://img.shields.io/github/actions/workflow/status/XbNz/osintea/phpunit.yml?label=Tests&logo=github&style=for-the-badge)
![PHPStan](https://img.shields.io/github/actions/workflow/status/XbNz/osintea/phpstan.yml?label=PHPStan&logo=github&style=for-the-badge)

![Mutation testing badge](https://img.shields.io/endpoint?style=for-the-badge&url=https://badge-api.stryker-mutator.io/github.com/XbNz/osintea/main)


![PNG](/art/logo.png) 
### The all-in-one network intelligence tool

## Supported Platforms
- MacOS (Darwin)

## Features

### We have command palettes!
<img src="/art/command_palette.png" width="379">

### And even light mode for the psychopaths
<img src="/art/ping_window_light.png" width="379" style="margin-left: 10px;">

### ICMP Ping (powered by [fping](https://fping.org/))
<img src="/art/ping_window.png" width="379">

### Really fast range to IP conversion
<img src="/art/range_to_ip.png" width="379">

### Organization to range with support for multiple database vendors
<img src="/art/organization_to_range.png" width="379">

### Polygon to range with support for multiple database vendors
<img src="/art/polygon_to_range.png" width="379">

### Fping is great in the terminal, but it's even better with a GUI
<img src="/art/fping_preferences.png" width="379">

### Database update manager
<img src="/art/database_preferences.png" width="379">

### Demo video
https://github.com/user-attachments/assets/06c4f73a-8b4b-45ae-922c-e1eef6b8f4c9


## Download & Installation
> The application is still in development and awaiting an upcoming beta release of NativePHP to become downloadable to all! Contributors can run the app in test mode.

## Contributing

### Authentication for `livewire/flux`
```bash
composer config http-basic.composer.fluxui.dev "${FLUX_USERNAME}" "${FLUX_LICENSE_KEY}"
```

### Installation

```bash
composer install
npx vite build
npm run dev
php artisan native:migrate
php artisan native:serve
```
