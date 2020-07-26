
### Laravel setup notes
#### Adding User UI:

> \> composer require "laravel/ui"

> \> php artisan ui vue --auth

> \> npm install

> \> npm run dev

#### Adding permissions package

> \> composer require spatie/laravel-permission

#### Publish the permissions migration and config

> \> php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

> \> php artisan config:clear

> \> php artisan migrate


### Add Roles

In tinker:

> \> $roleAdmin = Role::create(['name' => 'admin']);

> \> $roleUser = Role::create(['name' => 'user']);

Then assign admin user:

> \> $user->assignRole('admin');
