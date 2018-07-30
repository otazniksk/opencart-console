**Opencart 2.x Console tools for Developers**

Installation
------------

The recommended way to install is via Composer:

```
composer require otazniksk/opencart-console
```

- v1.0 requires PHP 5.6 or newer
- Tested on Windows 10 - WAMP

Available commands tools

- <b>Create new Extension</b> - <i>create:extension</i>

<br />
After the installation create file "opencart" in home directory your Opencart  or download <a href="https://github.com/otazniksk/opencart-console/blob/master/opencart">opencart</a> raw file.

> file: <b>opencart</b>


```php
#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use otazniksk\OpencartConsole\InitConsole;
$console = new InitConsole(__DIR__);

```

Commands
------------------

>php opencart create:extension


<br/>
<i>That's all!</i>
<br/>
<br/>


License
--------------
The MIT License (MIT)

Copyright (c) 2018 otaznik.sk

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.