# EDBAdminBundle
*A simple to use Symfony based CMS*

---

#### Install the bundle via Composer

```bash
composer require eduandebruijne/admin-bundle
```

---

#### Config for security.yaml

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

---

#### Command to create first admin user

```bash
bin/console admin:create-user <required:role> <required:username/email> <optional:password>
```


#### Example entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use EDB\AdminBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/** 
 * @ORM\Entity 
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

#### Example admin

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
