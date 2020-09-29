## TODO

- ~~Seed roles~~
- ~~Seed users~~
- ~~Admin dashboard~~
- ~~User dashboard~~
- DEFINE MVP (minimal viable product!!!)
- Tickets
    - ~~Disable admin user to create tickets !!!~~
    - Test the TicketService !!!
- Ticket CRUD
    - Read/View
    - Create
    - Update
    - Delete
    - Close
    
## MVP (Minimal Viable Product)
Features for the initial release:

- Admin and User users. 
    - Admin can be created only manually for now.
    - User can register via 
        - register form on the ticket system
        - Login with Envato
        - [login with Facebook]
        - [Login with Google]
        - [Login with Twitter]
    - Admin can view and reply to all tickets. Admin can't create tickets. 
    - User can only view and reply to own tickets. User can create tickets.
    
- Tickets
    - Ticket can be created, viewed, updated, closed and reopened.
    - Ticket can have many replies (from an admin and user who created the ticket).
    - Replies can be edited and deleted by an admin. User can only edit it.  
    - Tickets and replies can have attached files, like: text, pdf, docs and media files.
    - [Tickets and replies can have inserted links to video clips, which will be auto-converted to embedded players.]
    
- Taxonomies
    - Tickets can have categories (sub-categories will be supported). Created and assigned by the admin only.
    - Tickets can be assigned with tags. Created and assigned by user and admin. Tags are user-specific, meaning every user will have own set of tags.


- Settings
    - User
        - Signature
        - Personal info
        - Notification
        - [Language]
    - Admin
        - Signature
        - Personal info
        - Notifications
        - Ticket settings 
        - [Language]
        - [API credentials]

- Articles (KB) & FAQs
    - Admin can create/edit/delete articles.
    - Articles can have categories. It would be the categories from Taxonomies.
    - (FAQ can be added as an article page.)


- Footer link for a page with info about the ticket system and how it can be used by anybody.
     

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

### Refresh the App, migrations and tables data:

> \> php artisan migrate:fresh
> \> php artisan db:seed
