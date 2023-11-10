# Smart Fan Controller

**An automatic job to control a fan to improve the humidity in any room.**  
**It uses SwitchBot Meters to messure temperatures and SwitchBot Bot to toggle the Fan based on the Dew Point** 

## Requirements

- PHP 8.1+
- The application is tested on **Linux** (Ubuntu 19.04)
- It should also run on Windows and MacOS - but it was not tested yet

## Installation

1: Clone GitHub Repository
```bash
git clone https://https://github.com/arminwinkt/fan-controller-smart && cd fan-controller-smart
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


## License

This project is released under the permissive [MIT license](LICENSE.md).
