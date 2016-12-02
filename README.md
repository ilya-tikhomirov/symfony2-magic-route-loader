Loader module for Routing component
================

Generates routes collection without configuration file or route annotations

Configuration for Di component.

Add into `service.yml`
```yml
services:
  some.routing_loader:
  class: SomeBundle\Routing\Loader\MagicLoader
  tags:
    - { name: routing.loader }
```

And change in `routing.yml` type of loader to `magic`

Example
```
app:
    resource: '@AppBundle/Controller/'
    type:     generatedCollection
```