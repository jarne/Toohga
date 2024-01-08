#!/bin/sh

# Commands to execute when starting Toohga Docker container

# Build client to have up-to-date customization with env variables
cd client
yarn build
cd ..

# Run Apache2 web server
apache2-foreground
