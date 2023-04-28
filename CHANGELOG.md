# Changelog

## UNRELEASED

- The plugin is now compatible with Statamic's replacer based caching middleware.

## 1.4.1 (2022-05-12)

- **Security-Fix**: The signature of rendering URLs of dynamic partials is now properly checked -- [#a216e5a](https://github.com/alpshq/statamic-cache-evader/commit/a216e5a)

## 1.4.0 (2022-04-30)

- Statamic 3.3 is now required
- Fixed an issue which resulted in TokenMismatchExceptions when submitting forms in Statamic 3.3 -- [#09d4d04](https://github.com/alpshq/statamic-cache-evader/commit/09d4d04)

## 1.3.3 (2022-04-16)

- Text-Only responses of dynamic partials are now properly handled. -- [#84e2cfa](https://github.com/alpshq/statamic-cache-evader/commit/84e2cfa)

## 1.3.2 (2022-04-16)

- Empty responses of dynamic partials are now properly handled. -- [#427eb4b](https://github.com/alpshq/statamic-cache-evader/commit/427eb4b)

## 1.3.1 (2022-04-16)

- Class names of root elements of dynamic partials are now preserved -- [#567fca4](https://github.com/alpshq/statamic-cache-evader/commit/567fca481a87973f7a69564e1d8ec2f5b31ef05f) 

## 1.3.0 (2022-04-16)

- Support for dynamic partials was added: Review the [corresponding PR](https://github.com/alpshq/statamic-cache-evader/pull/2) or checkout the [updated read me](README.md) to learn how to integrate it.

## 1.2.0 (2022-02-22)

- Support for Statamic's [`full` cache strategy](https://statamic.dev/static-caching#file-driver) was added: Review the [corresponding PR](https://github.com/alpshq/statamic-cache-evader/pull/1) or checkout the [updated read me](README.md) to learn how to integrate it.

## 1.1.0 (2022-02-18)

- Frontend scripts are now automatically published after Addon installation.

## 1.0.0 (2022-02-18)

- Initial Release
