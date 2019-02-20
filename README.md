# userboard
Symfony Application

Setup Database Connection:
Update your database connection information in .env file 
at line no. 27
DATABASE_URL=mysql://<DB_USERNAME>:<DB_PASSWORD>@127.0.0.1:3306/<DB_NAME>
Execute console command 
$: php bin/console doctrine:database:create

Interface provided for adding a new user to start with:
Routes : 
/home/users/add - Add new User
/home/welcome - User dashboard / welcome page
/login - Login page

To protect unauthorized access to the protected pages, use the following:
go to /config/packages/security.yaml and uncomment the following:
    # - { path: ^/home, roles: ROLE_USER } (just remove # from there)
    please use the similar configurations for your own routes



Importing Users Via CSV:
PHP bin/console app:csv_importusers "<file path >"
    
    For example, place the file (csv_users.csv) in the csvuploads folder in the root directory.
    run the command as PHP bin/console app:csv_importusers "./csvuploads/csv_users.csv"
    
    

