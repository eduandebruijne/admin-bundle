# EDBAdminBundle
*A simple to use Symfony based CMS*

---

#### Install the bundle via Composer

```bash
composer require eduandebruijne/admin-bundle
```

---

#### Configure

Add bundle config to `config/packages/edb_admin.yaml`

```yaml
edb_admin:
  admin_icon: "image"
  admin_title: "BdB"
  cache_prefix: "media_cache"
  media_class: MEDIACLASS
  source_prefix: "media_source"
  user_class: App\Entity\User
```

Add routes to `config/routes/edb_admin.yaml`

```yaml
edb_dynamic:
  resource: .
  type: admin
  prefix: '%edb_admin_path%'

edb_admin:
  resource: '@EDBAdminBundle/Resources/config/routes.yaml'
  prefix: '%edb_admin_path%'
```

Make sure environment variables are available

```
ADMIN_PATH=
MEDIA_PATH=
OAUTH_GOOGLE_CLIENT_ID=
OAUTH_GOOGLE_CLIENT_SECRET=
```

#### Configure security

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
  - { path: ^/%env(ADMIN_PATH)%/login, roles: PUBLIC_ACCESS }
  - { path: ^/%env(ADMIN_PATH)%, roles: ROLE_ADMIN }
```

#### Install assets, update database and clear cache

```bash
bin/console assets:install
bin/console doctrine:schema:update --force
bin/console cache:clear
```

---

#### Create admin user

```bash
bin/console admin:create-user example@gmail.com ROLE_ADMIN
```

---

In order to use the admin panel, both AbstractUser and AbstractMedia should be implemented within the project

#### Implement User class

```php
<?php

namespace App\Entity;

use EDB\AdminBundle\Entity\AbstractUser;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class User extends AbstractUser
{
}
```

#### Implement Media class

```php
<?php

namespace App\Entity;

use EDB\AdminBundle\Entity\AbstractMedia;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Media extends AbstractMedia
{
}
```

At this point the admin panel should work completely. Now you can start adding your own entities and admins.

#### Example entity

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

#### Example admin

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
