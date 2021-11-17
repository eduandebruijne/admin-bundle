# EDBAdminBundle
*A simple to use Symfony based CMS*

#### Add bundle services and auto tagging to your project

------

Append services to `config/services.yaml`

```php
...
EDB\AdminBundle\Route\Loader:
    tags:
    	- { name: routing.loader }

_instanceof:
    EDB\AdminBundle\Admin\AdminInterface:
        tags: [ "app.admin" ]
    EDB\AdminBundle\EntityEvent\EntityEventHandlerInterface:
        tags: [ "app.entity_event_handler" ]
    EDB\AdminBundle\MenuBuilder\MenuItemInterface:
        tags: [ "app.custom_menu_item" ]

EDB\AdminBundle\Admin\Pool:
	arguments: [ !tagged app.admin ]

EDB\AdminBundle\EntityEvent\Pool:
	arguments: [ !tagged app.entity_event_handler ]

EDB\AdminBundle\MenuBuilder\MenuBuilder:
    arguments:
        $customMenuItems: !tagged_iterator app.custom_menu_item
```



#### Install the bundle via Composer

------

```bash
composer require eduandebruijne/admin-bundle
```



#### Enable bundle form theme

------

Append form themes to `config/packages/twig.yaml`

```yaml
twig:
    ...
    form_themes:
        - 'bootstrap_5_layout.html.twig'
        - '@EDBAdmin/form/theme.html.twig'
```



#### Configure bundle routes

------

Append routes to `config/routes/annotation.yaml`

```yaml
...
edb_admin:
    resource: .
    type: admin
    prefix: "%admin_path%"

edb_admin_media:
    resource: '@EDBAdminBundle/Controller'
    type: annotation
    prefix: "%admin_path%"
```



#### Adjust oneup_flysystem.yaml to use filesystem_source in default local adapter

------

```yaml
oneup_flysystem:
    adapters:
        public:
            local:
                location: '%kernel.project_dir%/public/media'
        private:
            local:
                location: '%kernel.project_dir%/var/uploads'
    filesystems:
        public_filesystem:
            adapter: public
        private_filesystem:
            adapter: private
```



#### Installing bundle assets and clearing cache

------

```bash
bin/console assets:install
bin/console doctrine:schema:update --force
bin/console cache:clear
```



#### Configure main firewall

------

```yaml
role_hierarchy:
    ROLE_ADMIN: ~

providers:
    oauth:
        id: EDB\AdminBundle\Security\GoogleUserProvider

firewalls:
    main:
        guard:
            authenticators:
                - EDB\AdminBundle\Security\GoogleAuthenticator

access_control:
    - { path: ^/%env(ADMIN_PATH)%/auth/google/connect, roles: PUBLIC_ACCESS }
    - { path: ^/%env(ADMIN_PATH)%/login, roles: PUBLIC_ACCESS }
    - { path: ^/%env(ADMIN_PATH)%, roles: ROLE_ADMIN }
```



#### Add Google client to KnpUOAuth2ClientBundle configuration

------

```yaml
knpu_oauth2_client:
    clients:
        google:
            type: google
            client_id: '%env(OAUTH_GOOGLE_CLIENT_ID)%'
            client_secret: '%env(OAUTH_GOOGLE_CLIENT_SECRET)%'
            redirect_route: connect_google_check
            redirect_params: {}
```



#### Add first admin user

------

```sql
INSERT INTO `user` (`id`, `created_at`, `updated_at`, `username`, `roles`) values (1, '2021-08-31 10:00:00', '2021-08-31 10:00:00', '{google-email-address}', '["ROLE_ADMIN"]')
```



#### Create entity

---
```php
<?php

namespace App\Entity;

use EDB\AdminBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Page extends BaseEntity
{
    /**
     * @ORM\Column
     */
    private ?string $title;

    public function __toString()
    {
        return $this->title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title)
    {
        $this->title = $title;
    }
}
```



#### Create admin

---
```php
<?php

namespace App\Admin;

use App\Entity\Page;
use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Admin\AdminInterface;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PageAdmin extends AbstractAdmin implements AdminInterface
{
    public function buildForm(FormCollection $collection)
    {
        $collection->add('title', TextType::class);
    }

    public function buildList(ListCollection $collection)
    {
        $collection->add('title');
    }

    public static function getAdminMenuTitle(): string
    {
        return 'Pages';
    }

    public static function getEntityClass(): string
    {
        return Page::class;
    }
}
```
