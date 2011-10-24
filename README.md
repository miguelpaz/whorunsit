WhoRunsIt
=========

WhoRunsIt is a front-end to the Companies House register of company directors. It's written in Symfony2, includes a parser for the Companies House proprietary file format, and uses Sphinx as its search engine. Pull requests welcome, though please open a ticket first to see if it's a change we'll accept to the live site before you start. 


Requirements
------------

* PHP 5.3.3 or newer
* Sphinx search 0.9.9 or newer
* A database supported by both Doctrine2 and Sphinx (e.g. MySQL)
* A copy of the Companies House directory of company appointments. Sorry, we can't provide this. We may be able to supply a small sample file.
* Apache


Installation
------------

* Clone the repository or download a tarball
* Several configuration files need to be copied and set up. They're provided as -dist files in the repository:
  * Copy and modify `web/.htaccess-dist` to `web/.htaccess`
  * Copy and modify `app/config/parameters.ini-dist` to `app/config/parameters.ini`
  * Copy and modify `sphinx/sphinx.conf-dist` to `sphinx/sphinx.conf`
* Install Sphinx. On Debian, it's `apt-get install sphinxsearch`. YMMV.
* Set up Symfony2: run `php bin/vendors install` from the application root. You'll need Git for this to work.
* Once you've set up your database details in `app/config/parameters.ini`, either create the database yourself or run `app/console doctrine:database:create`. The login details in parameters.ini will need the relevant permissions.
* Create the database schema: `app/console doctrine:schema:create`
* Import your data: `app/console whorunsit:rebuild path/to/your/datafiles`
* Run the Sphinx indexer: `indexer --config sphinx/sphinx.conf --all --rotate` (use the --rotate option if searchd is running)
* Set your web server/vhost DocumentRoot to the `web` directory and reload the web server.

