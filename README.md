# Welcome to Manevia!

Manevia is an open source PHP framework.  I know, it isn't like we need another one of those, but Manevia goes against the grain in a few different ways.  While I think it's a bit more than a micro framework, it definitely isn't a "kitchen sink" solution.  A few things to consider...

- **API Specific!** Manevia was originally conceived as a full stack framework, serving both front-end and back-end code.  It is now geared very specifically towards back-end API development.  This allows applications to be built in a more modern way, with a single API servicing multiple front end clients (_web, mobile apps, desktop, IoT, etc_). 

- **Simplicity & Minimalism** is a key concept.  The term "_framework_" is overly used these days as most frameworks grew from being a frame to build your code upon to containing many features as key components of your application right out of the box.  There is nothing wrong with that per say, but most applications use less than 20% of the features of popular frameworks.  That other 80% generally aids in complexity and/or inhibits performance.  PHP has an [outstanding package manager](https://getcomposer.org/)!  Rather than including functionality that may or may not be used and/or forcing developers to use those features in a specific way, Manevia purposely stays simple and minimal, allowing you to bolt on the exact features you need and nothing else.

- **No Framework Updates!**  One of the core goals of Manevia is to _NOT_ include any functionality that may be added via Composer.  While most "_frameworks_" build more and more features into their codebase, Manevia attempts to strip functionality out.  Therefore, once that application is started it becomes its own instance completely detached from the original Manevia framework codebase.  A key feature of this is that it is 100% OK to alter framework core files to fit your application's needs!  Think of Manevia as a starting point of a completely custom whiteboard application.

- **No ORM** is included with Manevia, at least not in a traditional sense.   It is designed to be as modular as possible.  You can use [Doctrine ORM](https://github.com/doctrine/orm), Eloquent ORM via the [illuminate/database](https://packagist.org/packages/illuminate/database) Composer package, or any other library your heart desires!  Manevia does have a [default model builder](https://github.com/jabarihunt/mysql-model-builder).  Rather than building migrations and seeds first, with the model builder you'll work in the opposite direction.  Instead, you'll first create a well designed database that is optimized for the way your application needs to consume data.  Then you will run the model builder (_a quick single line command_) that will build models that you can immediately begin using within Manevia.  You'll continue making any required changes to your database first, then run the model builder.  It will detect those changes and alter the models accordingly.  The default model builder may be removed by simply deleting the `jabarihunt/mysql-model-builder` package from Composer!

- **Docker** is the prefered contanerization platform for manevia.  This makes both creating dev environments and deploying applications dead simple (_as demonstrated below_)!  Manevia was developed with serverless containerized services in mind, such as [Google Cloud Run](https://cloud.google.com/run), [AWS Fargate](https://aws.amazon.com/fargate/), and [Digital Ocean App Platform](https://www.digitalocean.com/products/app-platform/).  You can setup and deploy applications on these platforms in minutes!

> _**Note:** Manevia is _NOT_ for everyone!  It was never intended to be.  If you like to trade convenience for performance (and I don't mean that in a negative way) then this framework probably isn't going to suit you.  If you honestly enjoy coding and were probably a DBA in another life then you'll fit right in!_

## 5 Minute Quick Start!

If you haven't already, [install Docker](https://docs.docker.com/get-docker/) on your machine.  While most of the major steps to get up an running fast have been automated, there are a few simple steps that you must do manually (for now)..

 1. Clone this repository into your working directory | `git clone https://github.com/jabarihunt/manevia.git .`
 2. Copy _[.env.example](.env.example)_ to _.env_ | `cp .env.example .env`
 3. Open the _.env_ file and edit your docker app and tag names if you so choose.  You can also change the port number that docker will use for your application.
 4. **_OPTIONAL:_** If using the default model builder, add the following to your .env file (with your credentials):
     ```
    # MYSQL
    MYSQL_HOST="your.db.host.name"
    MYSQL_DATABASE="databaseName"
    MYSQL_USERNAME="userName"
    MYSQL_PASSWORD="superSecretPassword"
    MYSQL_SOCKET=null
    MYSQL_PORT=3306
     ```
     > _**NOTE:** If using sockets, set `MYSQL_HOSTNAME` equal to `null` and set `MYSQL_SOCKET` to your socket string.  You should use one or the other.  **If both are set, it will default to using a TCP connection.**_
 5. Run Composer | `composer install`
 6. Run the start script | `./start.sh`
 
**THAT'S IT!** Your docker instance will build & run, then you may access your app at `http://localhost:8080/` (or whatever port you specified in _.env_).

## Application Structure

Like most modern frameworks, Manevia follows a MVC(ish) design pattern.  The directory structure is straight forward, everything is exactly where you'd expect it to be (_nothing buried in a sea of directories_).

```bash
.
├── app
│   ├── classes
│   │   └── Manevia
│   │       └── Utilities.php
│   ├── composer.json
│   ├── controllers
│   │   └── v1
│   │       ├── ExampleController.php
│   │       └── v1Controller.php
│   └── index.php
├── cli
│   └── manevia.php
├── .env.example
├── .gitattributes
├── .gitignore
├── Dockerfile
├── LICENSE.md
├── README.md
├── apache.conf
├── restart.sh
├── start.sh
└── stop.sh
```
## Making Manevia Your Own

Initial customization of Manevia to make it your own app is very simple, and generally just requires changes in the [app/composer.json](app/composer.json) file.  For example sake, we're going to make changes for a fictional application by the _ACME Corporation_ called _Anvil_.

### Application Details

In this example we will be changing the `name` and `description` properties of the application.   Since any [official properties](https://getcomposer.org/doc/04-schema.md#properties) of the [composer.json schema](https://getcomposer.org/doc/04-schema.md) may be added, we will also add the application `type` (_which defaults to "library"_):

```json
{
  "name": "acme/anvil",
  "description": "The ACME Anvil app is the absolute best way to knock your block off!",
  "type": "project",
  "config": {
    "optimize-autoloader": true,
    "classmap-authoritative": true
  },
  "require": {
    "ext-curl": "*",
    "ext-json": "*",
    "ext-mysqli": "*",
    "jabarihunt/mysql-model-builder": "*"
  },
  "require-dev": {
    "phpunit/phpunit": "*"
  },
  "autoload": {
    "psr-4": {
      "Manevia\\": ["classes/Manevia/"],
      "Manevia\\Controllers\\": ["controllers/v1/"]
    }
  }
}
```

### Namespacing

> _**TIP:** If you are unfamiliar with namespacing, SymfonyCasts has a great [5 minute video tutorial](https://symfonycasts.com/screencast/php-namespaces/namespaces) that is well worth the watch._

Adding & namespacing your own classes in Manevia is the same as in any other Composer based project using PSR-4.  It is recommended that you create a folder within the _app/classes_ directory to store your namespaced classes.  For example sake, we're going to add a namespace for our fictional application.  It's a simple two step process:

1. Create a folder to save your classes.  As recommended above, we're going to create a folder named "ACME" in the _app/classes_ directory.  Our new directory path from the project root will be `app/classes/ACME`.

2. Open the composer.json file, then add the namespace and associated folder under the `psr-4` section as shown below:

    > _**NOTE:** Do not remove the entries for "Manevia", as it will break the core features of the framework!_

    ```json
    {
      "name": "acme/anvil",
      "description": "The ACME Anvil app is the absolute best way to knock your block off!",
      "type": "project",
      "config": {
        "optimize-autoloader": true,
        "classmap-authoritative": true
      },
      "require": {
        "ext-curl": "*",
        "ext-json": "*",
        "ext-mysqli": "*",
        "jabarihunt/mysql-model-builder": "*"
      },
      "require-dev": {
        "phpunit/phpunit": "*"
      },
      "autoload": {
        "psr-4": {
          "ACME\\": ["classes/ACME"],
          "Manevia\\": ["classes/Manevia/"],
          "Manevia\\Controllers\\": ["controllers/v1/"]
        }
      }
    }
    ````
    
You can find extended documentation on autoloading and namespacing in Composer [here](https://getcomposer.org/doc/04-schema.md#autoload).

## Endpoints & Routing

Now that we have a basic Manavia instance up and running for our fictional ACME Anvil API, let's add an endpoint called `drop`.

### Routing

Routing in Manevia is done by convention rather than with explicit definition and tries to follow RESTful API best practices in doing so.  The expected URL path uses the following pattern:

    protocol://domain/version/endpoint/{resourceId)?query_component=query_component_value
    
- **protocol:** While all APIs _should_ use HTTPS, there is nothing that prevents the use of HTTP.

- **domain:** Manevia is completely domain agnostic, feel free to fashion any domain any way you see fit.

- **version:** API version numbers may use any numeric version convention that you like, but it must start with the letter "v".

- **endpoint:** Endpoint names must not have any spaces, be all lowercase, and use hyphens ("-") rather than underscores ("_").

- **resourceId:** While the resource ID is normally nummeric, it may be any string.
    
Taking this into consideration, our `drop` endpoint will have the follwing URL for local development: `https://localhost:8080/v1/drop`

### Controllers





## Contributing

1. Fork Repository
2. Create a descriptive branch name
3. Make edits to your branch
4. Squash (rebase) your commits
5. Create a pull request

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
