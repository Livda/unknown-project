# Symfony application in Docker

## First run

After you've pulled the repo launch all the containers and install all the dependencies with 

```bash
make create
```

You should have your brand new Symfony application run at [http://localhost](http://localhost)

### Possible issue

It's possible that the database creation fail. If so, just launch `make create-db`.

## Use this template

### Make usage

To list all the commands available run `make`, it'll display a help.

To restart the project, `make up` should be enough to have everything running.

### Encore usage (i.e. JS and CSS management)

This template provides [Encore](https://symfony.com/doc/current/frontend.html). The build of the assets in done in background by the `encore` container.


### Database usage

The database data are persisted on the system in the `database/` folder, to avoid the recreation of the database at each start of the project. You can access the database at this address `127.0.0.1` on the `3306` port.

