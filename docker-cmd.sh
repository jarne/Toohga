#!/bin/sh

# Commands to execute when starting Toohga Docker container

# Replace environment variable values in client dist
cd client
yarn run replace-env-variables
cd ..

# Run Apache2 web server
apache2-foreground
