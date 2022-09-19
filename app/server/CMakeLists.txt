cmake_minimum_required(VERSION 2.8)
project( OnlineResourceWatcherServerApp )
add_executable( OnlineResourceWatcherServerApp main.cpp )

include_directories( /usr/include )
link_directories( /usr/lib )

# SQLite3
target_link_libraries( OnlineResourceWatcherServerApp sqlite3 )

# Libcurl
target_link_libraries( OnlineResourceWatcherServerApp libcurl )

# MariaDB
target_link_libraries( OnlineResourceWatcherServerApp mysql )

# cmake -DCMAKE_BUILD_TYPE=Debug -DOMS_DEBUG=true
# cmake -DCMAKE_BUILD_TYPE=Release -DOMS_DEMO=true