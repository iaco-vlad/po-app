# Purchase Order App

This project requires Docker and npm to run. Follow the steps below to set up and run the project.

## Prerequisites

- Docker: Make sure you have Docker installed on your system. You can download it from the official Docker website: [https://www.docker.com](https://www.docker.com)
- npm: Ensure that you have npm (Node Package Manager) installed. npm is bundled with Node.js, which you can download from [https://nodejs.org](https://nodejs.org)

## Setup and Running the Project

1. **Start the Docker containers:**
   Open a terminal and navigate to the project directory. Run the following command to start the Docker containers:
```
composer up -d
```
This command will build and start the necessary containers defined in the `docker-compose.yml` file.

2. **Verify container status:**
   After running the previous command, ensure that all containers are up and running successfully. You can check the status of the containers by running:
```
docker-compose ps
```
Make sure all containers have a status of "Up" and that they have finished their build. It might take up to 10 minutes.

3. **Install the npm dependencies:**
   Before running the development server, you need to install the required npm dependencies. Run the following command:
```
npm install
```


3. 1. **Seed the database (optional):**
   If you want to populate your database with some initial data, you can run the database seeder. Execute the following command in the `php-fpm` container:
```
docker-compose exec php-fpm php artisan db:seed
```


4. **Run the development server:**
   Once the containers are up and running, you can start the development server using npm. Run the following command:
```
npm run dev
```
This command will start the development server and compile the necessary assets.

5. **Access the application:**
   After starting the development server, you can access the application by opening a web browser and navigating to [http://localhost:52000/](http://localhost:52000/) or to the appropriate URL and port specified in the terminal. 

That's it! You should now have the project up and running on your local machine.

## Additional Commands

- To stop the Docker containers, run:
```
composer stop
```
