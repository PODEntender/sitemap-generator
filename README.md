[Read in English](https://github.com/PODEntender/sitemap-generator/blob/master/README.en.md)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PODEntender/sitemap-generator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PODEntender/sitemap-generator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/PODEntender/sitemap-generator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PODEntender/sitemap-generator/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/PODEntender/sitemap-generator/badges/build.png?b=master)](https://scrutinizer-ci.com/g/PODEntender/sitemap-generator/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/PODEntender/sitemap-generator/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Sitemap Generator
---

`Sitemap Generator` é uma biblioteca que oferece uma forma genérica de desenvolver sitemaps para seu website.

O protocolo utilizado para a geração dos sitemaps é o descrito no site oficial: [https://www.sitemaps.org](https://www.sitemaps.org).

## Integrações e Adapters

Todos adapters desenvolvidos podem ser verificados no diretório `lib/Adapter`. Cada um conta com um `README.md` por onde
você poderá encontrar mais informações em como realizar a sua integração.

Atualmente o projeto conta com os seguintes adapters:

Adapter | README.md
------- | ---------
Jigsaw  | [Ver](https://github.com/PODEntender/sitemap-generator/blob/master/lib/Adapter/Jigsaw/README.md)

## Roadmap

- [ ] Adicionar suporte a `sitemap index` (ver issue #2)
- [ ] Criar suporte a providers para facilitar o trabalho dos adapters
