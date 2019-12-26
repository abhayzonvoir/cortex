# Cortex Tags Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](CONTRIBUTING.md).


## [v3.0.3] - 2019-12-18
- Add DT_RowId field to datatables
- Fix route regex pattern to include underscores
  - This way it's compatible with validation rule `alpha_dash`
- Fix `migrate:reset` args as it doesn't accept --step

## [v3.0.2] - 2019-10-14
- Update menus & breadcrumbs event listener to accessarea.ready
- Fix wrong dependencies letter case

## [v3.0.1] - 2019-10-06
- Refactor menus and breadcrumb bindings to utilize event dispatcher

## [v3.0.0] - 2019-09-23
- Upgrade to Laravel v6 and update dependencies

## [v2.2.1] - 2019-08-03
- Tweak menus & breadcrumbs performance

## [v2.2.0] - 2019-08-03
- Upgrade composer dependencies

## [v2.1.3] - 2019-06-03
- Enforce latest composer package versions

## [v2.1.2] - 2019-06-03
- Update publish commands to support both packages and modules natively

## [v2.1.1] - 2019-06-02
- Fix yajra/laravel-datatables-fractal and league/fractal compatibility

## [v2.1.0] - 2019-06-02
- Update composer deps
- Drop PHP 7.1 travis test
- Refactor migrations and artisan commands, and tweak service provider publishes functionality

## [v2.0.0] - 2019-03-03
- Require PHP 7.2 & Laravel 5.8
- Utilize includeWhen blade directive
- Refactor abilities seeding

## [v1.0.2] - 2019-01-03
- Rename environment variable QUEUE_DRIVER to QUEUE_CONNECTION
- Simplify and flatten create & edit form controller actions
- Tweak and simplify FormRequest validations
- Enable tinymce on all description and text area fields

## [v1.0.1] - 2018-12-22
- Update composer dependencies
- Add PHP 7.3 support to travis

## [v1.0.0] - 2018-10-01
- Support Laravel v5.7, bump versions and enforce consistency

## [v0.0.2] - 2018-09-22
- Too much changes to list here!!

## v0.0.1 - 2017-09-09
- Tag first release

[v3.0.3]: https://github.com/rinvex/cortex-tags/compare/v3.0.2...v3.0.3
[v3.0.2]: https://github.com/rinvex/cortex-tags/compare/v3.0.1...v3.0.2
[v3.0.1]: https://github.com/rinvex/cortex-tags/compare/v3.0.0...v3.0.1
[v3.0.0]: https://github.com/rinvex/cortex-tags/compare/v2.2.1...v3.0.0
[v2.2.1]: https://github.com/rinvex/cortex-tags/compare/v2.2.0...v2.2.1
[v2.2.0]: https://github.com/rinvex/cortex-tags/compare/v2.1.2...v2.2.0
[v2.1.2]: https://github.com/rinvex/cortex-tags/compare/v2.1.1...v2.1.2
[v2.1.1]: https://github.com/rinvex/cortex-tags/compare/v2.1.0...v2.1.1
[v2.1.0]: https://github.com/rinvex/cortex-tags/compare/v2.0.0...v2.1.0
[v2.0.0]: https://github.com/rinvex/cortex-tags/compare/v1.0.2...v2.0.0
[v1.0.2]: https://github.com/rinvex/cortex-tags/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/rinvex/cortex-tags/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/rinvex/cortex-tags/compare/v0.0.2...v1.0.0
[v0.0.2]: https://github.com/rinvex/cortex-tags/compare/v0.0.1...v0.0.2
