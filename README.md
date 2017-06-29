# Homeostasis Bundle

 `master` | `develop` |
|----------|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/build-status/master) | [![Build Status](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/HomeostasisBundle/build-status/develop) |

## Installation

```sh
composer require innmind/homeostasis-bundle
```

Enable the bundle by adding the following line in your app/AppKernel.php of your project:

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Innmind\HomeostasisBundle\InnmindHomeostasisBundle,
        );
        // ...
    }
    // ...
}
```

Then you need to specify the an actuator:

```yaml
innmind_homeostasis:
    actuator: service_id
```

The actuator service will need to implement the interface `Innmind\Homeostasis\Actuator`.

By default it uses the cpu and symfony logs as factors, but you can your own via the `factors` config key. To add a factor you need to create a service tagged with `innmind.homeostasis.factor` and with an `alias` attribute, this alias will need to be a key in the `factors` config. If you define a factory on your service then the data under your alias in the config will be injected in the factory service.

## Usage

To trigger the whole mechanism you just have to call the following code somewhere in your app (for exemple on `console.terminate` when an amqp consumer finished his job).

```php
$container->get('innmind.homeostasis.regulator')();
```
