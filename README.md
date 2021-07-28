# Welcome to Manevia!

Manevia is an open source PHP framework.  I know, it isn't like we need another one of those, but Manevia goes against the grain in a few different ways.  While I think it's a bit more than a micro framework, it definitely isn't a "kitchen sink" solution.  A few things to consider...

- **Simplicity & Minimalism** is a key concept.  The term "_framework_" is overly used these days as most frameworks grew from being a frame to build your code upon to containing many features as key components of your application right out of the box.  There is nothing wrong with that per say, but most applications use less than 20% of the features of popular frameworks.  That other 80% generally aids in complexity and/or inhibits performance.  PHP has an [outstanding package manager](https://getcomposer.org/)!  Rather than bolting on functionality that may or may not be used and forcing developers to use those features in a specific way, Manevia purposely stays simple and minimal, allowing you to bolt on the exact features you need and nothing else.

- **API Specific:** Manevia was originally conceived as a full stack framework, serving both front-end and back-end code.  It is now geared very specifically towards back-end API development.  This allows applications to be built in a more modern with, with a single API servicing multiple front end clients (_web, mobile apps, desktop, IoT, etc_). 

- **No ORM** is used in Manevia, at least not in a traditional sense.   It is designed to be as modular as possible.  You can use [Doctrine ORM](https://github.com/doctrine/orm), Eloquent ORM via the [illuminate/database](https://packagist.org/packages/illuminate/database) Composer package, or any other library your heart desires!  Manevia does have a [default model builder](https://github.com/jabarihunt/mysql-model-builder).  Rather than building migrations and seeds first, with the model builder you'll work in the opposite direction.  Instead, you'll first create a well designed database that is optimized for the way your application needs to consume data.  Then you will run the model builder (a quick single line command) that will build Models that you can immediately begin using within Manevia.

- **Docker** is the prefered contanerization platform for manevia.  This makes both creating dev environments and deploying applications dead simple (as demonstrated below)!  Manevia was developed with serverless containerized services in mind, such as [Google Cloud Run](https://cloud.google.com/run), [AWS Fargate](https://aws.amazon.com/fargate/), and [Digital Ocean App Platform](https://www.digitalocean.com/products/app-platform/).  You can setup a deploy applications on these platforms in minutes!

> **Note:** Manevia is _NOT_ for everyone!  It was never intended to be.  If you like to trade convenience for performance (and I don't mean that in a negative way) then this framework probably isn't going to suit you.  If you honestly enjoy coding and were probably a DBA in another lifetime, you'll fit right in!

## 5 Minute Quick Start!

If you haven't already, [install Docker](https://docs.docker.com/get-docker/) on your machine.  While most of the major steps to get up an running fast have been automated, there are a few simple steps that you must do manually (for now)..

 1. Clone this repository into your working directory | `git clone https://github.com/jabarihunt/manevia.git .`
 2. Copy _.env.example_ to _.env_ | `cp .env.example .env`
 3. Open the .env file and edit your docker app and tag names if you so choose.  You can also change the port number that docker will use for your application.
 4. _OPTIONAL:_ If using the default model builder, add the following to your .env file (with your credentials):
     ```
    # MYSQL
    MYSQL_HOST="your.db.host.name"
    MYSQL_DATABASE="databaseName"
    MYSQL_USERNAME="userName"
    MYSQL_PASSWORD="superSecretPassword"
    MYSQL_SOCKET=null
    MYSQL_PORT=3306
     ```
     > **NOTE:** If using sockets, set `MYSQL_HOSTNAME` equal to `null` and set `MYSQL_SOCKET` to your socket string.  You should use one or the other.  **If both are set, it will default to using a TCP connection.**
 5. Run Composer | `composer install`
 6. Run the start script | `./start.sh`
 
**THAT'S IT!**  Your docker instance will build & run, and you can access your app at `http://localhost:8080/`.

## Application Structure

Like most modern frameworks, Manevia follows a MVC(ish) design pattern.  The directory structure is straight forward, everything is exactly where you'd expect it to be (nothing buried in a sea of directories).

```bash
.
├── app
│   ├── classes
│   │   └── Manevia
│   │       └── Utilities.php
│   ├── composer.json
│   ├── composer.lock
│   ├── controllers
│   │   └── v1
│   │       ├── ExampleController.php
│   │       └── v1Controller.php
│   ├── index.php
│   └── tests
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

## CONTRIBUTING

1. Fork Repository
2. Create a descriptive branch name
3. Make edits to your branch
4. Squash (rebase) your commits
5. Create a pull request

## LICENSE

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
