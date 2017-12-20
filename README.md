Cloud migration project
=======================

Getting the project running on your local machine
-------------------------------------------------

1. You need [Vagrant](https://www.vagrantup.com/) and [Virtualbox](https://www.virtualbox.org) and [Git](https://git-scm.com/) 
installed on your PC

2. Check out / clone the project from Github

3. In the main folder of the project run the following command to set up and install a new virtual machine:
```
vagrant up
```

4. Once the VM is up and running, log into the virtual machine and install the required libraries:
```
> vagrant ssh
$ cd /vagrant/APP_ROOT/silex_web
$ composer install
```

5. Connect to the IP address of the box with your web browser and you should see a page

[http://192.168.33.123/test](http://192.168.33.123/test)



Extra stuff
-----------
Setting up a Silex application from scratch:
```
cd /vagrant/APP_ROOT
composer require silex/silex "~2.0"
```
