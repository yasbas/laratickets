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
    - Admin can view (and reply) to all tickets. Admin can't create tickets. 
    - User can only view (and reply) to own tickets. User can create tickets.
    
- Tickets
    - Ticket can be created, viewed, updated, closed and reopened.
    - Ticket can have many replies (from an admin and user who created the ticket).
    - Replies can be edited and deleted by an admin. User can only create and edit it.  
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


### Add Socialite and Envato login

First install Socialite official package:

(https://laravel.com/docs/8.x/socialite)

> \> composer require laravel/socialite 

Then add the Envato package from the extended socialite packages:
(https://laravel.com/docs/8.x/socialite)

> \> composer require socialiteproviders/envato

Add configuration to config/services.php

```
'envato' => [    
   'client_id' => env('ENVATO_KEY'),  
   'client_secret' => env('ENVATO_SECRET'),  
   'redirect' => env('ENVATO_REDIRECT_URI') 
 ],
```  

And then add the constants to the .env file (don't forget to change the ENVATO_REDIRECT_URI value with the actual one):

```
ENVATO_KEY=helpdesk-envato-login-wmdjwtiy
ENVATO_SECRET=TAdi42SN7wEqgq3GOXgCgUcPCTNJUjJM
ENVATO_REDIRECT_URI=http://envato-login.local/login/envato/callback
```

Remove Laravel\Socialite\SocialiteServiceProvider from your providers[] array in config\app.php if you have added it already.

Add \SocialiteProviders\Manager\ServiceProvider::class to your providers[] array in config\app.php.

For example:

```
'providers' => [
    // a whole bunch of providers
    // remove 'Laravel\Socialite\SocialiteServiceProvider',
    \SocialiteProviders\Manager\ServiceProvider::class, // add
];
```

Next, add provider event listener:

Configure the package's listener to listen for SocialiteWasCalled events.

Add the event to your listen[] array in app/Providers/EventServiceProvider

For example:

```
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Envato\\EnvatoExtendSocialite@handle',
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];
```

Add routes to routes/web.php

```
Route::get('login/envato', 'Auth\LoginController@redirectToProvider');
Route::get('login/envato/callback', 'Auth\LoginController@handleProviderCallback');
```

Then add the login methods to LoginController:

```
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::with('envato')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $envatoUser = Socialite::driver('envato')->user();

        dd($envatoUser);

        /*$user = User::where('provider_id', $githubUser->getId())->first();

        if (!$user) {
            // add user to database
            $user = User::create([
                'email' => $githubUser->getEmail(),
                'name' => $githubUser->getName(),
                'provider_id' => $githubUser->getId(),
            ]);
        }

        // login the user
        Auth::login($user, true);

        return redirect($this->redirectTo);*/
    }
```

Next, add the "Login with Envato" button to the login form.

Edit resources/views/auth/login.blade.php, below the current Login button:

```
<button type="submit" class="btn btn-primary">
    {{ __('Login') }}
</button>
```

Add the Envato login button:

```
<a href="login/envato" type="submit" class="btn btn-primary">
    Login with Envato
</a>

```
