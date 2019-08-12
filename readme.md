#Multi Tenancy

##Flow Config

###Introduction

Flow Config is a key value configuration platform built on top of doctrine. It provides an PHP API for setting configuration at the platform that can be set by an install, and then set for a user, or other entity. Defaults are set in a single location, rather than scattering them through the code.

###Usage

1. Register `FlowConfigServiceProvider` in your `bootstrap/app.php`

2. Add an XMLDriver for `CodeFoundation\FlowConfig` namespace on your doctrine mapping driver chain. The driver should load maps from `vendor/code-foundation/flow-config/src/Entity/DoctrineMaps` and look for files with extension `.orm.xml` 

3. Implement `LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigurableInterface` on an entity that wishes to be able to write to flow config.

```php
class User implements LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigurableInterface
{
    public function getEntityType(): string
    {
        // This should be unique for each entity.
        return 'user';
    }

    public function getEntityId(): string
    {
        return $this->id;
    }
}
```
3. Use injectable `FlowConfigInterface` to then write configurations as needed.

```php
class DoesSomething
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigInterface
     */
    private $flowConfig;

    public function __construct(\LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigInterface $flowConfig
    ) {
        $this->flowConfig = $flowConfig;
    }

    public function getConfig(): ?string
    {
        return $this->flowConfig->get('key', 'default');
    }

    public function setConfig(): void
    {
        $this->flowConfig->set('key', 'value');
    }
}
```