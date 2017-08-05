set PATH=%PATH%;C:\php;C:\php\ext
set MAGICK_HOME=C:\php\imagemagick
start php -S localhost:8000
ping 127.0.0.1>NUL
start http://localhost:8000/
