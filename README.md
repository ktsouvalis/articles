<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About This Project

This project is a Laravel-based application for fetching articles from various sources and serve them with the native API. Follow the instructions below to set up and run the project.
Documentation of the API is inside /routes/api.php

## Setting the environment for demonstration

### Step 1: Clone the Repository

```sh
mkdir articles
cd articles
git clone https://github.com/ktsouvalis/articles.git .
```

### Step 2: Install Dependencies

```sh
composer install
```

### Step 3: Set Up Environment Variables

Copy the example environment file and set up your environment variables:

```sh
copy .env.example .env
php artisan key:generate
```

Inside the `.env` file, set your database connection details for MySQL or the path to your SQLite file.  
Also set your enviroment variables for the sources e.g.

```env
GUARDIAN_API_URL=content.guardianapis.com/search
GUARDIAN_API_KEY=your-guardian-key
NYTIMES_API_URL=api.nytimes.com/svc/search/v2/articlesearch.json
NYTIMES_API_KEY=your-nytimes-key
NEWSAPI_API_URL=newsapi.org/v2/everything
NEWSAPI_API_KEY=tour-news-api-key
```


### Step 4: Run Migrations

Run the database migrations to set up the necessary tables:

```sh
php artisan migrate
```

## Running the Application

You will need to run the following commands in three separate terminal windows:

### Terminal 1: Start the Development Server

```sh
php artisan serve
```

### Terminal 2: Start the Queue Worker

```sh
php artisan queue:work
```

### Terminal 3: Run the Custom Command

```sh
php artisan app:call-sources
```

### OR: Run the scheduler and wait

```sh
php artisan schedule:work
```

## Extending the Application

Pick an API source of your preference, and follow these steps to integrate it into the application:

1. **Add a New Source in `app/Services/SourceKeeper.php`:**
    - Locate the `SourceKeeper` class in your project.
    - Use the `setSources` method to add a new record to the sources array for your chosen API source. This will typically involve specifying the source name and any necessary configuration details. You have to study your choice's API Documentation

2. **Create Your Own Mapper in `app/Services/`:**
    - Create a new Mapper class for your API source. This class should implement the necessary Mapper interface. Use the ExampleMapper and the other existing mappers as a reference to understand the required methods and structure.

## License

The project is licensed under the [MIT license](https://opensource.org/licenses/MIT).
