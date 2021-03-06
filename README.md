# Octo - Content Management System
[![Build Status](http://ec2-52-10-26-150.us-west-2.compute.amazonaws.com/build-status/image/1)]

## Using Octo for a new site
You can find a working example site in the [Octo Skeleton](https://github.com/Block8/Octo-Skeleton) project.

To get started:

* Clone: `git clone git@github.com:Block8/Octo-Skeleton.git <your site name>`
* Move into your new project directory: `cd <your site name>`
* If you want to try out the example site:
  * Create a database and import into it the content from `octo-skeleton.sql`
  * Modify `siteconfig.php` to point to that database
* If you want to create a new site:
  * Remove the .git folder and create as a new repo: `rm -Rf .git && git init`
  * Modify the `siteconfig.php` file as necessary for your project
  * Rename the `Example` namespace and modify the code within it for your project

## Dependencies

### Block 8
* **[b8 framework](https://github.com/block8/b8framework)** - Underlying PHP framework for the system

### Third Party
* [Bootstrap CSS](http://getbootstrap.com/) - CSS framework for the CMS and most sites based upon it
* [Admin LTE](https://github.com/almasaeed2010/AdminLTE) - Bootstrap theme and extended CSS theme for the CMS admin area.
* [File type icons](http://treetog.deviantart.com/art/File-Type-Icons-199693041) - File type icons
* [Symfony/Console](https://github.com/symfony/console) - Foundation for creating console commands (in our case, `./octocmd`)
* [PHP 5.5 Password Compat](https://github.com/ircmaxell/password_compat) - Polyfill for the PHP 5.5 `password_hash()` / `password_verify()` functionality.
* [Twitter PHP library](https://github.com/dg/twitter-php) - Twitter API library.
