# WordleMonde

WordleMonde is a Wordle Like, with a geographic thematic, for french players.

### Prerequisites

1. Check php (7.4 minimum) and composer are installed
2. Check mysql is installed

You do not need Webpack or node for this project.

### Install

1. Clone this project
2. Run `composer install`
3. Create .env.local from .env file and fill it with your database login
4. Run `php bin/console d:d:c` to create database
5. Run `php bin/console d:m:m` to build tables in database
6. Run `php bin/console d:f:l` to load fake data for dev environment

### Working

1. Run `symfony server:start` to launch your local php web server
2. Let's dev !

If you need admin access, you have to create a new account and update the user with ["ROLE_ADMIN"] in database

The step by step is for usual Symfony developers. If you can't install for any reason, do not hesitate to contact me : guillaumeharari@hotmail.com 
## License

MIT License

Copyright (c) 2021 guillaumeharari@hotmail.com

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
