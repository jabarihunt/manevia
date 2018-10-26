Welcome to Manevia!
===================
Manevia is an open source PHP framework.  I know, it isn't like we need another one of those, but Manevia goes against the grain in a few different ways.  While I think it's a bit more than a micro framework, it definitely isn't a "kitchen sink" solution.  A few things to consider...

- **Simplicity & Minimalism** is a key concept.  The term "framework" is overly used these days as most frameworks grew from being a frame to build your code upon to containing many features as key components of your application right out of the box.  There is nothing wrong with that per say, but most applications use less than 20% of the features of popular frameworks.  That other 80% generally aids in complexity and/or inhibits performance.  PHP has an [outstanding package manager](https://getcomposer.org/)!  Rather than bolting on functionality that may or may not be used and forcing developers to use those features in a specific way, Manevia purposely stays simple and minimal, allowing you to bolt on the exact features you need and nothing else.

- **No ORM** is used in Manevia, at least not in a traditional sense.  Rather than building migrations and seeds first, with Manevia you'll work in the opposite direction.  Instead, you'll first create a well designed database that is optimized for the way your application needs to consume data.  Then you will run Manevia's model builder (a quick single line command) that will build Models that you can immediately begin using. The days of queries with 8 JOIN statements _should_ be a thing of the past with Manevia.

> **Note:** Manevia is _NOT_ for everyone!  It was never intended to be.  If you like to trade convenience for performance (and I don't mean that in a negative way) then this framework probably isn't going to suit you.  If you honestly enjoy coding and were probably a DBA in another lifetime, you'll fit right in!

----------

Quick Start _(Development Environment)_
-------------
You'll need to install [Vagrant](https://www.vagrantup.com/) as well as compatible virtualization software such at [VirtualBox](https://www.virtualbox.org/).  While most of the major steps to get up an running fast have been automated, there are a few simple steps that you must do manually (for now)..

 1. Clone this repository into your working directory | `git clone https://github.com/jabarihunt/manevia.git .`
 2. Add an entry to your host file for your domain | _Example:_ `192.168.33.10 manevia.test`
 3. Copy _.env.example_ to _.env_ | `cp .env.example .env`
 4. Open the .env file and update the IP address to the one you set in your host file | _Example:_ `DEVELOPMENT_IP_ADDRESS = "192.168.33.20"`
 5. Run Vagrant (and get a cup of coffee) | `vagrant up`

> **Tip:** For dev environments, set the `GENERATE_SELF_SIGNED_CERTIFICATE` equal to `"1"` in your .env file. This will create a self-signed SSL certificate and key for your dev environment.

----------

Application Structure
-------------
Like most modern frameworks, Manevia follows the MVC design pattern.  Views and controllers behave as they do in most other frameworks for the most part, models will differ some.  The directory structure is straight forward, everything is exactly where you'd expect it to be (nothing buried in a sea of directories).

.
|-- backup/
|-- cli/
|---- build_docs/
|-------- 000-default.conf
|---- model_builder_docs/
|---- build.sh
|---- model_builder_docs.sh
|- controllers/
|- core/
|- crons/
|- css/
|- images/
|- js/
|- migrations/
|- models/
|- namespaces/
|- views/
|- .env.example
|- .gitignore
|- .htaccess
|- composer.json
|- index.php
|- readme.md
|- Vagrantfile


----------


Starting A Project
-------------

#### <i class="icon-file"></i> Create A Database

#### <i class="icon-file"></i> Run The Model Builder

#### <i class="icon-file"></i> Create A Controller & View

----------

Project Future
-------------


Contributing To The Project
-------------
