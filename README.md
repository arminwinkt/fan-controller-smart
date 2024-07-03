# Smart Fan Controller

**Automatically regulate room humidity with this intelligent fan controller.**  
This application leverages SwitchBot Meters to measure temperatures and SwitchBot Bots to control the fan. Its primary function is to enhance room humidity by allowing fresh air in, particularly when the outdoor humidity is lower than indoors.

## Requirements

- You need at least 2 SwitchBot Meters
   - One for Indoors, one for outdoor measurements
- You either need one SwitchBot Bot to control the action (start and stop the Fan to let air in)
- Or a Shelly Smart Plug to control the action (giving power to a fan)
- PHP 8.1+
- Composer
- The application is tested on **Linux** (Ubuntu 23.04)
- It should also run on Windows and MacOS - but it was not tested yet

## Installation

1: Clone GitHub Repository
```bash
git clone https://github.com/arminwinkt/fan-controller-smart && cd fan-controller-smart
```

2: Run composer install
```bash
$ composer install
```

3: **Add SwitchBot API credentials**
```bash
$ cp .env.sample .env
```

5: Adapt the Config to your needs
```
$ nano ./src/config.php
```

4: Run the controller
```bash
$ ./bin/console fan:controller auto
``` 
for more details run `./bin/console fan:controller --help`


## Run with Docker

1: Build Docker Image
```bash
$ docker build -t fancontroller .
```

2: Run
```bash
$ docker run -it --rm fancontroller
```


## License

This project is released under the permissive [MIT license](LICENSE.md).
