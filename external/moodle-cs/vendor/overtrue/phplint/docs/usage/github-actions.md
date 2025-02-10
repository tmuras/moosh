# GitHub Actions

## Use case 1

Quick start, if your PHP runtime set up is not important for you.

```yaml
jobs:
  php-lint:
    name: Linting with overtrue/phplint

    runs-on: ubuntu-latest

    steps:
      - name: Lint PHP files

        uses: overtrue/phplint@main
        with:
          path: .
          options: --exclude=vendor
```

## Use case 2

Otherwise, if you want to detect specific PHP features used by scripts depending of your PHP runtime, then use this case.

```yaml
jobs:
  php-lint:
    name: "Linting with overtrue/phplint"

    runs-on: "${{ matrix.operating-system }}"

    strategy:
      fail-fast: false

      matrix:
        operating-system:
          - "ubuntu-20.04"
          - "ubuntu-22.04"

        php-version:
          - "8.1"
          - "8.2"

    steps:
      - name: Checkout Code
        uses: actions/checkout@v3
        with:
          fetch-depth: 0
          repository: overtrue/phplint

      - name: Setup PHP runtime
        uses: shivammathur/setup-php@v2
        with:
          php-version: "${{ matrix.php-version }}"
          coverage: "none"

      - name: Lint PHP files
        run: |
          curl -Ls https://github.com/overtrue/phplint/releases/latest/download/phplint.phar -o /usr/local/bin/phplint
          chmod +x /usr/local/bin/phplint
          /usr/local/bin/phplint -vvv --no-cache
```

Follows steps: 

- retrieve source code to check with [actions/checkout](https://github.com/actions/checkout)
- set up the PHP runtime you want to use with [shivammathur/setup-php](https://github.com/shivammathur/setup-php) 
- download the latest (or specific) version of the PHAR distribution 
- and finally run PHPLint as usual, with a YAML config file or console command options
