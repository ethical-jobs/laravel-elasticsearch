# Laravel elasticsearch package

- Model observers for syncing Elasticsearch to Eloquent: indexing, reindexing and deleting documents.
- Artisan commands to manage an Elasticsearch index
- Eloquent hydrators for hydrating Eloquent models from Elasticsearch results
- StdClass hydrators for hydrating Objects from Elasticsearch results
- Multi process, high speed document indexing
- Distributed multi-process indexing-operations logging to slack for better visibility and debugging
- Extensive integration and unit test suite

## Running test suite

Some setup is required to run the test suite in this package as it requires docker to spin up an elasticsearch instance 
to test against.

### Using PHP Storm for testing
To do this in PHP Storm takes a little config as it requires you to setup docker as your remote tester.

#### Step 1: Configure Docker Daemon
Open up `Settings` -> `Build, Execution, Deployment` -> `Docker` and select `Docker for Mac`. 
PHP Storm should configure everything it needs to connect to Docker daemon from your local installation of Docker.
> NOTE: you will need to install Docker for Mac to use this setup

#### Step 2: Configure PHP interpreter to use Docker container
Open up `Settings` -> `Languages and Frameworks` -> `PHP` and select the elipsis next to `CLI interpreter`.

Create a new PHP CLI interpreter from Docker using the Configuration from `./docker-compose.yml`. It should 
automatically configure paths, etc based on your interpreter. 

#### Step 3: Configure PHP Test Frameworks
Open up `Settings` -> `Languages and Frameworks` -> `PHP` -> `Test Frameworks` and add a new `PHP by remote 
interpreter`. Select the CLI Interpreter you just created in Step 2 and apply config.

#### Step 4: Run tests in PHP Storm
All dependencies should be working now
