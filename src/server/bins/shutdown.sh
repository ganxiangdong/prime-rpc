echo 'shutdown...'
pid=$(pidof swoole_manager_test)
kill -15 "$pid"
echo 'done'
