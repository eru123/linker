# Linker Framework

PHP Framework for Building REST API

### Documentation

Documentation coming soon.

# Installation

## Creating Projects with linker-template

### Via composer

```bash
composer create-project eru123/linker-template <ProjectName>
```

### Via git

```bash
git clone https://github.com/eru123/linker-template.git <ProjectName>
```

## Installing core library

Do this if you know how core files works

### Via Composer

```bash
composer require eru123/linker
```

### For Contribution

```bash
git clone https://github.com/eru123/linker.git
cd linker
composer install
```

Then send pull request

# build

```bash
# disable phar.readonly and phar.require_hash mode via cli
php -d phar.readonly=0 -d phar.require_hash=0 compile
# If phar.readonly and phar.require_hash is already disabled in php.ini
php compile

# The project build directory is `dist`
```

## Example/Template

- [Public Filehost](https://github.com/eru123/linker-example-filehost)
