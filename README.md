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

use Doctrine\ORM\Mapping as ORM;
use EDB\AdminBundle\Entity\AbstractUser;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User extends AbstractUser
{
}
```

#### Create Media Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use EDB\AdminBundle\Entity\AbstractMedia;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
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

#### Create admin user

```bash
bin/console admin:create-user <required:role> <required:username/email> <optional:password>
```

#### Optionally: Create Admins

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

## Examples

#### Example Page Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use EDB\AdminBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/** 
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Page extends BaseEntity
{
    /**
     * @ORM\Column
     */
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
