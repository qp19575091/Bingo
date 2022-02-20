
----------
### This is a Bingo game played on the web.

### Use `memery` not database to store data.

Installation
----------

```
composer install
```

Then create .env file and edit database credentials there

```
cp .env.example .env
```
After run command you can find .env has copy from .env.example

Then generate APP_KEY
```
php artisan key:generate
```
 After run command you can find APP_KEY in .env file has created


Start serve
```
php artisan serve
```
Then [http://127.0.0.1:8000/api/room](http://127.0.0.1:8000/api/room) will work.

Start
-----
 ### You also can see `api doc` [here](http://localhost/Bingo/public/docs/index.html)


End Point | Description | Method |
|--|--|--|
| [api/room](http://127.0.0.1:8000/api/room) | Create a new room | POST | 
| [api/room](http://127.0.0.1:8000/api/roomn) | Show a room  | GET | 
| [api/room](http://127.0.0.1:8000/api/room) | Delete a room  | DELETE | 
| [api/room](http://127.0.0.1:8000/api/room) | User can join a room  | PUT |
| [api/game](http://127.0.0.1:8000/api/game) |The selected number becomes zero  | PUT | 
| [api/game](http://127.0.0.1:8000/api/game) | Store numeric values to fill bingoArr  | POST | 
