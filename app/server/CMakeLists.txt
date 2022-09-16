cmake_minimum_required(VERSION 2.8)
project( OnlineResourceWatcherServerApp )
add_executable( OnlineResourceWatcherServerApp main.cpp )

# SQLite3
include_directories( /usr/include )
link_directories( /usr/lib )
target_link_libraries( OnlineResourceWatcherServerApp sqlite3 )

# cmake -DCMAKE_BUILD_TYPE=Debug -DOMS_DEBUG=true
# cmake -DCMAKE_BUILD_TYPE=Release -DOMS_DEMO=true