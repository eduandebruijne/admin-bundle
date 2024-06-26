# EDBAdminBundle
*A simple to use Symfony based CMS*


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
