echo 'Reloading...'
pid=$(pidof swoole_manager_test)
kill -USR1 "$pid"
echo 'Reloaded'
