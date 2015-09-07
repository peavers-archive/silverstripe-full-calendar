#!/usr/bin/env bash

#==============
# Install required packages
#==============
requiredPackages() {

    apt-get update

    # Additional packages that are required by this script
    apt-get install -y -q curl npm nodejs-legacy

    # Install these globally as required
    npm install -g grunt-cli

}

#==============
# Install for clean setup
#==============
rubyInstall() {

    # Remove current ruby
    apt-get purge ruby

    # Required packages
    \curl -L https://get.rvm.io | bash -s stable

    # Enable RVM
    source ~/.rvm/scripts/rvm

    # Ruby
    rvm requirements
    rvm install ruby
    rvm use ruby --default

    # Gems
    gem update --system
    gem install sass
    gem install scss_lint

}

#==============
# Initiate and clone submodule
#==============
submodule() {

    # Make sure you're in a git directory before trying to create a submodule
    if [ -d ".git" ]; then
        git submodule init
        git submodule add https://gitlab.cwp.govt.nz/build-tools/grunt-base.git
    else
        git init
        submodule;
    fi

}

#==============
# Remove any old files and import new ones to project root
#==============
setupGrunt() {

    # remove Gruntfile.js if exist
    if [ -f Gruntfile.js ]; then
        rm Gruntfile.js
    fi

    # remove package.json if exist
    if [ -f package.json ]; then
        rm package.json
    fi

    # Create syslinks
    ln -s grunt-base/Gruntfile.js ./
    ln -s grunt-base/package.json ./

    # Install package.json dependencies
    npm install

    clear;

    echo 'COMPLETE: Type "grunt" to start watching!'

}

#==============
# Menu
#==============
main() {

    clear

    until [ "$REPLY" = "q" ]; do
        echo '#-----------------------------------------------#'
        echo '#   Grunt setup                                 #'
        echo '#-----------------------------------------------#'
        echo ''
        echo '1.  Install ruby & gems (for a clean server)'
        echo '2.  Install grunt-base'
        echo '3.  Install everything!'
        echo ''
        echo '#-----------------------------------------------#'
        echo 'q.  Quit'
        echo ''
        read -p 'Command : ' REPLY
        case $REPLY in
            1) clear && requiredPackages && rubyInstall;;
            2) clear && submodule &&  setupGrunt;;
            [Qq]*) clear && quit ;;
        esac
    done

}

#==============
# Call the menu
#==============
main

