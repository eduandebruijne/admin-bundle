# EDBAdminBundle
*A simple to use Symfony based CMS*

## Requirements

* PHP 8.4 or later within PHP 8.x (`^8.4`), including PHP 8.5.
* Symfony 7.4.x (`7.4.*`).
* The GD PHP extension for image processing.

## Upgrading to 13.0

PHP 8.2/8.3 are no longer supported. The dependency requirements now target
Symfony 7.4, DoctrineBundle 3.3, DBAL 4.5, ORM 3.7, Glide 4.2,
OneupFlysystemBundle 4.14 and Twig 3.30. Direct Symfony requirements allow
only 7.4.x patch updates. Other dependencies allow compatible patch and minor
updates through Composer's caret constraints.

When upgrading an application, follow the
[DoctrineBundle 3 upgrade guide](https://github.com/doctrine/DoctrineBundle/blob/3.3.x/UPGRADE-3.0.md).
Remove obsolete Doctrine configuration options such as `enable_lazy_ghost_objects`,
`auto_generate_proxy_classes`, `proxy_dir` and `proxy_namespace`.

Image processing now uses Glide 4 and Intervention Image 4. Applications that
directly use these libraries or customize image processing should review the
[Glide changelog](https://glide.thephpleague.com/4.0/changelog/) and
[Intervention Image upgrade guide](https://image.intervention.io/v4/getting-started/upgrade).

Custom form types overriding bundle methods must declare the matching return
types: `void` for form-building, view and options methods, and `string` for
`getParent()`.

## Compatibility checks

After installing dependencies, run `composer validate` and
`composer check-platform-reqs`. Test the consuming Symfony application's
database, authentication, forms and media flows when upgrading dependencies.


## Installation Instructions

#### Install using Composer

```bash
composer require eduandebruijne/admin-bundle
```

#### Create User Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use EDB\AdminBundle\Entity\AbstractUser;

#[Entity]
#[HasLifecycleCallbacks]
class User extends AbstractUser
{
}
```

#### Create Media Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use EDB\AdminBundle\Entity\AbstractMedia;

#[Entity]
#[HasLifecycleCallbacks]
class Media extends AbstractMedia
{
}
```

#### Update config in config/packages/security.yaml

```yaml
role_hierarchy:
    ROLE_ADMIN: ~

providers:
    user:
        entity:
            class: App\Entity\User
            property: username

firewalls:
    main:
        custom_authenticators:
            - EDB\AdminBundle\Security\GoogleAuthenticator
        form_login:
            provider: user
            login_path: login
            check_path: check_form_login
            default_target_path: dashboard
        entry_point: form_login
        logout:
            path: logout

access_control:
    - { path: ^/%env(EDB_ADMIN_PATH)%/login, roles: PUBLIC_ACCESS }
    - { path: ^/%env(EDB_ADMIN_PATH)%, roles: ROLE_ADMIN }
```

#### Use entities in config/packages/edb_admin.yaml

```yaml
edb_admin:
    media_class: App\Entity\Media
    user_class: App\Entity\User
```

#### Get ready

* Make sure the `DATABASE_URL` in your `.env` file is correct
* Create and update the database schema
* Create your first admin user

```bash
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --complete --force
bin/console admin:create-user ROLE_ADMIN <required:username/email> <optional:password>
```

#### Optionally: Create User Admin

```php
<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use EDB\AdminBundle\Admin\AbstractUserAdmin;

class UserAdmin extends AbstractUserAdmin
{
    public function getEntityClass(): string
    {
        return User::class;
    }
}

```

#### Optionally: Create Media Admin

```php
<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Media;
use EDB\AdminBundle\Admin\AbstractMediaAdmin;

class MediaAdmin extends AbstractMediaAdmin
{
    public function getEntityClass(): string
    {
        return Media::class;
    }
}
```

## Other examples

#### Example Page Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use EDB\AdminBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
#[HasLifecycleCallbacks]
class Page extends BaseEntity
{
    #[Column(type: 'string')]
    private ?string $title;

    public function __toString(): ?string
    {
        return $this->title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
}
```

#### Example Page Admin

```php
<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Page;
use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Admin\AdminInterface;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PageAdmin extends AbstractAdmin implements AdminInterface
{
    public function buildForm(FormCollection $collection): void
    {
        $collection->add('title', TextType::class);
    }

    public function buildList(ListCollection $collection): void
    {
        $collection->add('title');
    }

    public function getAdminMenuTitle(): string
    {
        return 'Pages';
    }

    public function getEntityClass(): string
    {
        return Page::class;
    }
}
```
