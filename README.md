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
        form_login:
            provider: user
            login_path: login
            check_path: check_form_login
            default_target_path: dashboard

access_control:
    - { path: ^/%env(ADMIN_PATH)%/login, roles: PUBLIC_ACCESS }
    - { path: ^/%env(ADMIN_PATH)%, roles: ROLE_ADMIN }
```

---

#### Create admin user

```bash
bin/console admin:create-user <username> <password> ROLE_ADMIN
```

At this point the admin panel should work completely. Now you can start adding your own entities and admins.

#### Example entity

```php
<?php

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
