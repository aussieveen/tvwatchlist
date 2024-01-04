- [ ] Set up github actions to build docker image and push to dockerhub
- [ ] Go through code and tidy up
- [ ] Add unit testing

# TV Watchlist

This is a single page web application to allow a user to create a watchlist of TV shows.

## Deployment

In order to deploy this application, you will need first build the container before running it.

In both cases you will need to have an API key/pin for the TVDB API.

### Building and running the container

If you're planning to run the application on a remote server, you will need to build the container locally and then push it to a container registry. I'm using Dockerhub for this, but you can use any container registry you like. 

Alternatively, you can also build the container on the remote server. This will mean that you don't need to push the container to a registry but you will need to have Git installed on the remote server.

You will also need to access to a MongoDB instance.

#### Steps

1. Build the container using the production Dockerfile, which will install all the dependencies and build the application.

```bash
docker build -t tvwatchlist:latest -f docker/php/Dockerfile.prod .
```
_N.B. If you're building on one architecture and deploying on another, you will need to include the --platform for the architecture you're planning to deploy to avoid any unexpected errors._

2. Tag the container with the registry URL and push it to the registry.

_If you built on where you're planning to host, you can skip straight to step 4._

```bash
docker tag tvwatchlist:latest <registry_url>/tvwatchlist:latest
docker push <registry_url>/tvwatchlist:latest
```

3. SSH into the remote server and pull the container from the registry.

```bash
docker pull <registry_url>/tvwatchlist:latest
```

4. Run the container.

As part of the run command, you will need to provide the following environment variables:
- MONGODB_URL - This is the URL to your MongoDB instance
- TVDB_APIKEY - This is the API key for the TVDB API
- TVDB_PIN - This is the PIN for the TVDB API

You also need to a port to expose the application on.
```bash
docker run
  -d
  --name=<name>
  --net='bridge'
  -e 'MONGODB_URL'=<mongodb_url>
  -e 'TVDB_APIKEY'=<tvdb_apikey>
  -e 'TVDB_PIN'=<tvdb_pin>
  -p '<hostport>:80/tcp' tvwatchlist:latest
```
