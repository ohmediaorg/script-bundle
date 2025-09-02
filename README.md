# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/script-bundle"
}
```

Then run `composer require ohmediaorg/script-bundle:dev-main`.

Import the routes in `config/routes.yaml`:

```yaml
oh_media_script:
    resource: '@OHMediaScriptBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.
