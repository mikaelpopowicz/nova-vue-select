[![GitHub stars](https://img.shields.io/github/stars/mikaelpopowicz/nova-vue-select.svg?style=flat-square)](https://github.com/mikaelpopowicz/nova-vue-select/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/mikaelpopowicz/nova-vue-select.svg?style=flat-square)](https://github.com/mikaelpopowicz/nova-vue-select/network)
[![GitHub issues](https://img.shields.io/github/issues/mikaelpopowicz/nova-vue-select.svg?style=flat-square)](https://github.com/mikaelpopowicz/nova-vue-select/issues)
[![GitHub last commit](https://img.shields.io/github/last-commit/mikaelpopowicz/nova-vue-select.svg?style=flat-square)](https://github.com/mikaelpopowicz/nova-vue-select/commits)

# Laravel Nova Vue select

## Introduction

Provides a capability of auto-completed searching resource.

Based on [Vue-multiselect](https://vue-multiselect.js.org/)

## Installation

You can install this [Laravel Nova](https://nova.laravel.com) field via composer:

```bash
composer require mikaelpopowicz/nova-vue-select
```

## Usage

```php
// in your Nova Resource

NovaVueSelect('Field label', 'attribute', OtherResource::class),
```

