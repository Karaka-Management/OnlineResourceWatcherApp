cmake_minimum_required(VERSION 2.8)
project( OnlineResourceWatcherServerApp )
add_executable( OnlineResourceWatcherServerApp main.cpp )

set(CMAKE_CXX_FLAGS "-march=native -msse2 -mavx -maes -fpermissive")

include_directories( /usr/include )
link_directories( /usr/lib )

# SQLite3
target_link_libraries( OnlineResourceWatcherServerApp sqlite3 )

# MariaDB
target_link_libraries( OnlineResourceWatcherServerApp mysql )

# Postgresql
target_include_directories( OnlineResourceWatcherServerApp PRIVATE /usr/include/postgresql )
target_link_libraries( OnlineResourceWatcherServerApp libpq )

# Libcurl
target_link_libraries( OnlineResourceWatcherServerApp libcurl )

# cmake -DCMAKE_BUILD_TYPE=Debug -DOMS_DEBUG=true
# cmake -DCMAKE_BUILD_TYPE=Release -DOMS_DEMO=true