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

VueSelect('Field label', 'attribute', OtherResource::class),
```

## Filter

Create a Nova filter and make it inherit from VueSelectFilter. You may override constructor to set the Resource you want to fetch. 

```php
<?php

namespace App\Nova\Filters;

use App\Nova\User;
use Illuminate\Http\Request;
use Mikaelpopowicz\NovaVueSelect\VueSelectFilter;

class UserFilter extends VueSelectFilter
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
    
    public function apply(Request $request, $query, $value)
    {
        return $query->where('user_id', '=', $value);
    }
}
```
